<?php

namespace App\Http\Controllers;

use App\Models\Community\AttendanceLog;
use App\Models\Company;
use App\Models\CustomerReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CustomerReportController extends Controller
{
    public function index()
    {
        return $this->processFilter()->paginate(request("per_page") ?? 10);
    }

    public function stats()
    {
        $Query = CustomerReport::query()
            ->select("customer_reports.date", "customer_reports.total_hrs", "customer_reports.branch_id as br_id")
            ->selectRaw('(SELECT AVG(total_hrs)) as avg_total_hours')
            ->selectRaw('(SELECT MIN(total_hrs)) as min_total_hours')
            ->selectRaw('(SELECT MAX(total_hrs)) as max_total_hours')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Male" AND attendance_logs.age_category != "CHILD" THEN 1 END) as male_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Male" AND attendance_logs.age_category = "CHILD"   THEN 1 END) as child_male_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Male" AND attendance_logs.age_category = "YOUNGER" THEN 1 END) as male_younger_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Male" AND attendance_logs.age_category  = "ADULT" THEN 1 END) as male_adult_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Male" AND attendance_logs.age_category  = "SENIOR" THEN 1 END) as male_senior_count')



            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Female" AND attendance_logs.age_category != "CHILD" THEN 1 END) as female_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Female" AND attendance_logs.age_category = "CHILD"   THEN 1 END) as child_female_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Female" AND attendance_logs.age_category = "YOUNGER" THEN 1 END) as female_younger_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Female" AND attendance_logs.age_category  = "ADULT" THEN 1 END) as female_adult_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.Gender = "Female" AND attendance_logs.age_category  = "SENIOR" THEN 1 END) as female_senior_count')



            ->selectRaw('COUNT(CASE WHEN attendance_logs.age_category = "CHILD"  THEN 1 END) as child_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.age_category = "YOUNGER" THEN 1 END) as younger_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.age_category = "ADULT" THEN 1 END) as adult_count')
            ->selectRaw('COUNT(CASE WHEN attendance_logs.age_category = "SENIOR" THEN 1 END) as senior_count')
            ->selectRaw('COUNT(CASE WHEN customers.type = "vip" THEN 1 END) as vip_customer_count')

            ->selectRaw('COUNT(CASE WHEN customers.status = "blocklisted" THEN 1 END) as blocklisted_customer_count')
            ->selectRaw('COUNT(CASE WHEN customers.type = "normal" THEN 1 END) as normal_customer_count')
            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "in" THEN 1 END) as in_count')
            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "in" AND attendance_logs.Gender = "Male" AND attendance_logs.age_category != "CHILD" THEN 1 END) as in_male_count')
            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "in"  AND attendance_logs.Gender = "Female" AND attendance_logs.age_category != "CHILD" THEN 1 END) as in_female_count')
            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "in"  AND attendance_logs.age_category = "CHILD"  THEN 1 END) as in_child_count')

            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "out" THEN 1 END) as out_count')

            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "out" AND attendance_logs.Gender = "Male" AND attendance_logs.age_category != "CHILD" THEN 1 END) as out_male_count')
            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "out"  AND attendance_logs.Gender = "Female" AND attendance_logs.age_category != "CHILD" THEN 1 END) as out_female_count')
            ->selectRaw('COUNT(CASE WHEN customer_reports.status = "out"  AND attendance_logs.age_category = "CHILD"  THEN 1 END) as out_child_count')

            ->selectRaw('(SELECT COUNT(*) 
                  FROM (
                      SELECT UserID
                      FROM attendance_logs
                      GROUP BY UserID
                      HAVING COUNT(*) > 2
                  ) AS repeated_users) AS repeated_customer_count');

        $Query->when(request()->filled("branch_id"), function ($q) {
            return $q->selectRaw('(SELECT occupancy FROM company_branches WHERE id = ' . request("branch_id") . ') as occupancy');
        })->when(!request()->filled("branch_id"), function ($q) {
            return $q->selectRaw('(SELECT SUM(occupancy) FROM company_branches WHERE company_id = ' . request("company_id") . ') as occupancy');
        });
        return   $Query->join('customers', 'customer_reports.user_id', '=', 'customers.system_user_id')
            ->join('attendance_logs', 'attendance_logs.id', '=', 'customer_reports.in_id')

            ->where('customers.company_id', request("company_id"))
            ->whereBetween('customer_reports.date', [request("from_date"), request("to_date")])
            ->when(request()->filled("branch_id"), fn ($q) => $q->where('br_id', request("branch_id")))
            ->with("branch_for_stats_only")
            ->groupBy('customer_reports.date')
            ->paginate(request("per_page") ?? 10);
    }




    public function store(Request $request)
    {
        return $this->render($request->company_id ?? 0, $request->date ?? date("Y-m-d"), $request->UserIds, true);
    }

    public function print(Request $request)
    {
        $data = $this->processFilter()->get()->toArray();

        if ($request->debug) return $data;

        $chunks = array_chunk($data, 10);

        return Pdf::setPaper('a4', 'landscape')->loadView('pdf.customer.report', [
            "chunks" => $chunks,
            "company" => Company::whereId(request("company_id") ?? 0)->first(),
            "params" => $request->all(),

        ])->stream();
    }
    public function download(Request $request)
    {
        $data = $this->processFilter()->get()->toArray();

        if ($request->debug) return $data;

        $chunks = array_chunk($data, 10);

        return Pdf::setPaper('a4', 'landscape')->loadView('pdf.customer.report', [
            "chunks" => $chunks,
            "company" => Company::whereId(request("company_id") ?? 0)->first(),
            "params" => $request->all(),

        ])->download();
    }
    public function render($companyId, $date, $userIds = [], $customRender = false)
    {
        $params = [
            "company_id" => $companyId,
            "date" => $date,
            "custom_render" => $customRender,
            "UserIds" => $userIds,
        ];

        if (!$customRender) {
            $userIds = AttendanceLog::where("company_id", $companyId)
                ->where("checked", false) // Only today's records
                ->where("user_type", "Customer")
                ->whereDate("LogTime", '=', $date) // Only today's records
                ->distinct("UserID", "company_id")
                ->pluck('UserID');
        }

        $userLogs = AttendanceLog::whereDate("LogTime", '=', $date) // Only today's records
            //->where("checked", false)
            ->where("user_type", "Customer")
            ->whereIn("UserID", $userIds)
            ->where("company_id", $companyId)
            ->distinct("LogTime", "UserID", "company_id")
            ->with(["device", "customer"])
            ->orderBy("LogTime", "asc")
            ->get()
            ->groupBy('UserID');


        //update atendance table with shift ID if shift with employee not found 
        if (count($userLogs) == 0) {
            return "No Record found";
        }

        $items = [];
        $message = "";

        $in_ids = [];
        $out_ids = [];
        foreach ($userLogs as $key => $logs) {



            $logs = $logs->toArray() ?? [];

            $firstLog = collect($logs)->filter(function ($record) {
                return isset($record["device"]["function"]) && ($record["device"]["function"] !== "Out");
            })->first();

            $lastLog = collect($logs)->filter(function ($record) {
                return isset($record["device"]["function"]) && ($record["device"]["function"] !== "In");
            })->last();

            if (!$firstLog) {
                continue;
            }

            $item = [];

            $item = [
                "user_id" =>   $firstLog["UserID"],
                "company_id" =>   $firstLog["company_id"],
                "branch_id" =>   $firstLog["branch_id"],
                "in_id" => $firstLog["id"],
                "status" => "in",
                "out_id" => "0",
                "total_hrs" => "0",
            ];

            if ($item["in_id"])
                $in_ids[] =   $item["in_id"];

            if ($lastLog) {
                $item["out_id"] = $lastLog["id"] ?? 0;
                $item["status"] = "out";
                $item["total_hrs"] = $this->getTotalHrsMins($firstLog["time"] ?? 0, $lastLog["time"] ?? 0);


                $out_ids[] = $item["out_id"];
            }

            $item["date"] = $params["date"];
            $items[] = $item;
        }
        //return json_encode($items);
        if (!count($items)) {
            return '[' . $date . " " . date("H:i:s") . '] No data found' . $message;
        }

        try {
            $user_ids = array_column($items, "user_id");
            $model = CustomerReport::query();
            $model->whereIn("user_id", $user_ids);
            $model->where("date", $date);
            $model->delete();
            $model->insert($items);


            AttendanceLog::where("company_id", $companyId)->whereIn("id", $in_ids)->update(["log_type" => 'in']);
            AttendanceLog::where("company_id", $companyId)->whereIn("id", $out_ids)->update(["log_type" => 'out']);
            AttendanceLog::where("company_id", $companyId)->whereIn("UserID", $userIds)->update(["checked" => true, "checked_datetime" => date('Y-m-d H:i:s')]);
            return $message = "[" . $date . " " . date("H:i:s") .  "].  Affected Ids: " . json_encode($userIds) . " " . $message;
        } catch (\Throwable $e) {
            return  $message = "[" . $date . " " . date("H:i:s") .  "]. " . $e->getMessage();
        }

        return ($message);
    }

    public function processFilter()
    {
        $query = CustomerReport::query();

        $query->when(request()->filled("branch_id"), function ($q) {
            $q->whereHas("customer", function ($qu) {
                $qu->where('branch_id', request("branch_id"));
            });
        });

        $query->when(request()->filled("status"), function ($q) {
            $q->where('status', request("status"));
            $q->where('company_id', request("company_id"));
        });

        $query->when(request()->filled("type"), function ($q) {
            $q->whereHas("customer", function ($qu) {
                $qu->where('type', request("type"));
                $qu->where('company_id', request("company_id"));
            });
        });

        $query->when(request()->filled("age_category"), function ($query) {
            $query->where(function ($q) {
                $q->whereHas("in_log", function ($qu) {
                    $qu->where('age_category', request("age_category"));
                    $qu->where('company_id', request("company_id"));
                });
            });
        });

        $query->when(request()->filled("Gender"), function ($query) {
            $query->where(function ($q) {
                $q->whereHas("in_log", function ($qu) {
                    $qu->where('Gender', request("Gender"));
                    $qu->where('company_id', request("company_id"));
                });
            });
        });

        $query->when(request()->filled("UserID"), function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('UserID', request("UserID"));
                $qu->where('company_id', request("company_id"));
            });
        });


        $query->when(request()->filled("DeviceID"), function ($query) {
            $query->where(function ($q) {
                $q->whereHas("in_log", function ($qu) {
                    $qu->where('DeviceID', request("DeviceID"));
                    $qu->where('company_id', request("company_id"));
                });
                $q->orWhereHas("out_log", function ($qu) {
                    $qu->where('DeviceID', request("DeviceID"));
                    $qu->where('company_id', request("company_id"));
                });
            });
        });
        $query->when(request()->filled("from_date"), function ($q) {
            $q->where('date', '>=', request("from_date"));
            $q->where('company_id', request("company_id"));
        });
        $query->when(request()->filled("to_date"), function ($q) {
            $q->where('date', '<=', request("to_date"));
            $q->where('company_id', request("company_id"));
        });

        $query->with("customer", "in_log", "out_log");

        return $query;
    }
}
