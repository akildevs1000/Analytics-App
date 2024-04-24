<?php

namespace App\Http\Controllers;

use App\Models\Community\AttendanceLog;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Customer;
use App\Models\CustomerReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerReportController extends Controller
{
    public function index()
    {
        return $this->processFilter()->paginate(request("per_page") ?? 10);
    }

    public function stats()
    {
        $branchReportQuery = CompanyBranch::query();
        $branchReportQuery->where("company_id", request("company_id"));
        $occupanySum =  $branchReportQuery->where("id", request("branch_id") ?? 0)->sum("occupancy");

        $customerReportQuery = CustomerReport::query();
        $customerReportQuery->where("company_id", request("company_id"));
        $customerReportQuery->whereBetween("date", [request("from_date"), date("Y-m-d", strtotime(request("to_date") . " +1 day"))]);
        $customerReportsByDates = $customerReportQuery->get()->groupBy("date");

        $result = collect(); // Create a collection to store paginated results

        foreach ($customerReportsByDates as $dateKey => $date) {

            $total_hrs = $date->pluck("total_hrs");

            $statusGroup = $date->groupBy("status");

            $inOut = $statusGroup->map->count();

            $type = $date->groupBy("type")->map->count();


            $age_category = $date->groupBy("age_category")->map->count();

            $gender = $date->groupBy("gender")->map->count();

            $gender_age_category = $date->groupBy(["gender", "age_category"]);

            $status_label = $date->groupBy("status_label")->map->count();

            $result->push([
                "date" => $dateKey,

                "in_count" => $inOut["in"] ?? 0,
                "in_male_count" =>  $statusGroup["in"]->where("gender", "Male")->count(),
                "in_female_count" =>  $statusGroup["in"]->where("gender", "Female")->count(),
                "in_child_count" =>  $statusGroup["in"]->where("gender", "Child")->count(),

                "out_count" => $inOut["out"] ?? 0,
                "out_male_count" =>  $statusGroup["out"]->where("gender", "Male")->count(),
                "out_female_count" =>  $statusGroup["out"]->where("gender", "Female")->count(),
                "out_child_count" =>  $statusGroup["out"]->where("gender", "Child")->count(),


                "vip_count" => $type["vip"] ?? 0,
                "normal_count" => $type["normal"] ?? 0,


                "whitelisted" => $status_label["whitelisted"] ?? 0,
                "blocklisted" => $status_label["blocklisted"] ?? 0,


                "male_count" => $gender["Male"] ?? 0,
                "female_count"  => $gender["Female"] ?? 0,

                "male_senior_count" => isset($gender_age_category["Male"]["SENIOR"]) ?  $gender_age_category["Male"]["SENIOR"]->count() : 0,
                "female_senior_count" => isset($gender_age_category["Female"]["SENIOR"]) ?  $gender_age_category["Female"]["SENIOR"]->count() : 0,

                "child_male_count" => isset($gender_age_category["Male"]["CHILD"]) ?  $gender_age_category["Male"]["CHILD"]->count() : 0,
                "child_female_count" => isset($gender_age_category["Female"]["CHILD"]) ?  $gender_age_category["Female"]["CHILD"]->count() : 0,

                "male_younger_count" => isset($gender_age_category["Male"]["YOUNGER"]) ?  $gender_age_category["Male"]["YOUNGER"]->count() : 0,
                "female_younger_count" => isset($gender_age_category["Female"]["YOUNGER"]) ?  $gender_age_category["Female"]["YOUNGER"]->count() : 0,

                "male_adult_count" => isset($gender_age_category["Male"]["ADULT"]) ?  $gender_age_category["Male"]["ADULT"]->count() : 0,
                "female_adult_count" => isset($gender_age_category["Female"]["ADULT"]) ?  $gender_age_category["Female"]["ADULT"]->count() : 0,


                "adult_count" => $age_category["ADULT"] ?? 0,
                "senior_count" => $age_category["SENIOR"] ?? 0,
                "child_count" => $age_category["CHILD"] ?? 0,
                "younger_count" => $age_category["YOUNGER"] ?? 0,

                "repeated_customers" => $date->groupBy("user_id")->count() ?? 0,
                "min_hrs" => $total_hrs->min() ?: 0,
                "max_hrs" => $total_hrs->max() ?: 0,
                "avg_hrs" => $total_hrs->average() ?: 0,
                "occupancy" => $occupanySum
            ]);
        }

        return $result;
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

        // echo json_encode($userLogs);die;


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
                return true;
                return isset($record["device"]["function"]) && ($record["device"]["function"] !== "Out");
            })->first();

            $lastLog = collect($logs)->filter(function ($record) {
                return true;
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
                "age_category" => $firstLog["age_category"],
                "gender" => $firstLog["Gender"],
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

                $item["age_category"] = $lastLog["age_category"];
                $item["gender"] = $lastLog["Gender"];

                $out_ids[] = $item["out_id"];
            }

            $item["date"] = $params["date"];
            $items[] = $item;
        }
        // return json_encode($items);
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

        $query->with("branch");

        $query->when(request()->filled("branch_id"), function ($q) {
            $q->where('branch_id', request("branch_id"));
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

        $query->when(request()->filled("age_category"), function ($q) {
            $q->where('age_category', request("age_category"));
        });

        $query->when(request()->filled("gender"), function ($q) {
            $q->where('gender', request("gender"));
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


    public function CustomerStatsReport(Request $request)
    {
        $branchReportQuery = CompanyBranch::query();
        $branchReportQuery->where("company_id", request("company_id"));
        $occupanySum =  $branchReportQuery->where("id", request("branch_id") ?? 0)->sum("occupancy");

        $customerReportQuery = CustomerReport::with("branch_for_stats_only");

        $customerReportQuery->where("company_id", request("company_id"));
        if (request("branch_id")) {
            $customerReportQuery->where("branch_id", request("branch_id"));
        }
        $customerReportQuery->whereBetween("date", [request("from_date"), date("Y-m-d", strtotime(request("to_date") . " +0 day"))]);
        $customerReportsByDates = $customerReportQuery->get()->groupBy("date");
        $result = collect(); // Create a collection to store paginated results

        $weekendsList = (new CompanyBranchController)->getWeekendsList($request);
        //get highestfootfall count


        $HighestPeakHoursWeekdays = $this->getHighestPeakHours($request, $weekendsList, false);
        $HighestPeakHoursWeekEnds = $this->getHighestPeakHours($request, $weekendsList, true);

        foreach ($customerReportsByDates as $dateKey => $date) {

            $total_hrs = $date->pluck("total_hrs");

            $statusGroup = $date->groupBy("status");

            $inOut = $statusGroup->map->count();

            $type = $date->groupBy("type")->map->count();


            $age_category = $date->groupBy("age_category")->map->count();

            $gender = $date->groupBy("gender")->map->count();

            $gender_age_category = $date->groupBy(["gender", "age_category"]);

            $status_label = $date->groupBy("status_label")->map->count();

            $child_male_count = isset($gender_age_category["Male"]["CHILD"]) ?  $gender_age_category["Male"]["CHILD"]->count() : 0;
            $child_female_count = isset($gender_age_category["Female"]["CHILD"]) ?  $gender_age_category["Female"]["CHILD"]->count() : 0;

            $result->push([

                "highest_peak_hours_weekDays" => $HighestPeakHoursWeekdays,
                "highest_peak_hours_weekEnds" => $HighestPeakHoursWeekEnds,

                "branch" => $date->pluck("branch_for_stats_only")->first(),
                "date" => $dateKey,

                "in_count" => $inOut["in"] ?? 0,
                "in_male_count" =>  $statusGroup["in"]->where("gender", "Male")->where("age_category", "!=", "Child")->count(),
                "in_female_count" =>  $statusGroup["in"]->where("gender", "Female")->where("age_category", "!=", "Child")->count(),
                "in_child_count" =>  $statusGroup["in"]->where("age_category", "Child")->count(),

                "out_count" => $inOut["out"] ?? 0,
                "out_male_count" =>  $statusGroup["out"]->where("gender", "Male")->where("age_category", "!=", "Child")->count(),
                "out_female_count" =>  $statusGroup["out"]->where("gender", "Female")->where("age_category", "!=", "Child")->count(),
                "out_child_count" =>  $statusGroup["out"]->where("age_category", "Child")->count(),


                "vip_count" => $type["vip"] ?? 0,
                "normal_count" => $type["normal"] ?? 0,


                "whitelisted" => $status_label["whitelisted"] ?? 0,
                "blocklisted" => $status_label["blocklisted"] ?? 0,


                "male_count" => $gender["Male"] ? $gender["Male"] - $child_male_count : 0,
                "female_count"  =>  $gender["Female"] ? $gender["Female"] -  $child_female_count : 0,

                "male_senior_count" => isset($gender_age_category["Male"]["SENIOR"]) ?  $gender_age_category["Male"]["SENIOR"]->count() : 0,
                "female_senior_count" => isset($gender_age_category["Female"]["SENIOR"]) ?  $gender_age_category["Female"]["SENIOR"]->count() : 0,

                "child_male_count" => $child_male_count,
                "child_female_count" => $child_female_count,

                "male_younger_count" => isset($gender_age_category["Male"]["YOUNGER"]) ?  $gender_age_category["Male"]["YOUNGER"]->count() : 0,
                "female_younger_count" => isset($gender_age_category["Female"]["YOUNGER"]) ?  $gender_age_category["Female"]["YOUNGER"]->count() : 0,

                "male_adult_count" => isset($gender_age_category["Male"]["ADULT"]) ?  $gender_age_category["Male"]["ADULT"]->count() : 0,
                "female_adult_count" => isset($gender_age_category["Female"]["ADULT"]) ?  $gender_age_category["Female"]["ADULT"]->count() : 0,


                "adult_count" => $age_category["ADULT"] ?? 0,
                "senior_count" => $age_category["SENIOR"] ?? 0,
                "child_count" => $age_category["CHILD"] ?? 0,
                "younger_count" => $age_category["YOUNGER"] ?? 0,

                "repeated_customers" => $date->groupBy("user_id")->count() ?? 0,
                "min_hrs" => $total_hrs->min() ?: 0,
                "max_hrs" => $total_hrs->max() ?: 0,
                "avg_hrs" => $total_hrs->average() ?: 0,
                "occupancy" => $occupanySum,
                "weekendsList" => $weekendsList
            ]);
        }

        if (!request()->filled("noPagination")) {


            $dataCollection = new Collection($result);
            $perPage = request()->has('per_page') ? request()->get('per_page') : 10;
            $page = request()->has('page') ? request()->get('page') : 1;
            $currentPageItems = $dataCollection->slice(($page - 1) * $perPage, $perPage)->all();
            return  $paginator = new LengthAwarePaginator(
                $currentPageItems, // Items for the current page
                $dataCollection->count(), // Total items
                $perPage, // Items per page
                $page, // Current page
                ['path' => request()->url()] // Additional options, like the base URL
            );
        } else {
            return  $result;
        }


        return $result;
    }


    public function getHighestPeakHours($requst, $weekendsList, $filterWeekEnds)
    {
        $attendanceLogs = AttendanceLog::where("company_id", request("company_id"));
        if (request("branch_id")) {
            $attendanceLogs->where("branch_id", request("branch_id"));
        }
        $attendanceLogs = $attendanceLogs->whereBetween("LogTime", [request("from_date"), date("Y-m-d", strtotime(request("to_date") . " +1 day"))])->pluck("LogTime");




        $hourlyVisitorCount = [];

        foreach ($attendanceLogs as $log) {

            $dayName = strtolower(date("l", strtotime($log)));
            if ($filterWeekEnds) {
                if ($weekendsList[$dayName] == 0) {

                    continue;
                }
            } else {
                if ($weekendsList[$dayName] == 1) {

                    continue;
                }
            }
            $hour = date('H', strtotime($log));

            // Increment visitor count for the corresponding hour
            if (!isset($hourlyVisitorCount[$hour])) {
                $hourlyVisitorCount[$hour] = 1;
            } else {
                $hourlyVisitorCount[$hour]++;
            }
        }
        // Copy the array before sorting
        $sortedVisitorCount = $hourlyVisitorCount;

        // Sort the array in descending order
        arsort($sortedVisitorCount);

        // Get the top 3 highest hours
        $top3Highest = array_slice(array_keys($sortedVisitorCount), 0, 2);

        // Sort the array in ascending order
        asort($sortedVisitorCount);

        // Get the top 3 lowest hours
        $top3Lowest = array_slice(array_keys($sortedVisitorCount), 0, 2);

        return ["top3Highest" => $top3Highest, "top3Lowest" => $top3Lowest];
    }
    public function CustomerStatsSumBetweenDatesReport(Request $request)
    {
        $branchReportQuery = CompanyBranch::query();
        $branchReportQuery->where("company_id", request("company_id"));


        $customerReportQuery = CustomerReport::with("branch_for_stats_only");

        $customerReportQuery->where("company_id", request("company_id"));
        if (request("branch_id")) {
            $customerReportQuery->where("branch_id", request("branch_id"));
        }
        $customerReportQuery->whereBetween("date", [request("from_date"), date("Y-m-d", strtotime(request("to_date") . " +0 day"))]);
        $customerReports  = $customerReportQuery->get();


        $dayCounts = [
            'WeekDay' => ['Male' => 0, 'Female' => 0, 'Child' => 0],
            'WeekEnd' => ['Male' => 0, 'Female' => 0, 'Child' => 0],

        ];
        $weekendsList = (new CompanyBranchController)->getWeekendsList($request);
        foreach ($customerReports as $report) {
            $gender = $report->gender;
            $gender = $report->gender;
            $dayName = strtolower(date("l", strtotime($report->date)));
            if ($weekendsList[$dayName] == 0) {
                if (($gender === 'Male' || $gender === 'Female') && $report->age_category != 'Child') {
                    $dayCounts["WeekDay"][$gender]++;
                } else {
                    $dayCounts["WeekDay"]["Child"]++;
                }
            } else {
                if (($gender === 'Male' || $gender === 'Female') && $report->age_category != 'Child') {
                    $dayCounts["WeekEnd"][$gender]++;
                } else {
                    $dayCounts["WeekDay"]["Child"]++;
                }
            }
        }

        return  $dayCounts;


        // return $customerReports = [
        //     "total" => $customerReports->count(),
        //     "male_count" => $customerReports->where("gender", "Male")->where("age_category", "!=", "Child")->count(),
        //     "female_count" => $customerReports->where("gender", "Female")->where("age_category", "!=", "Child")->count(),
        //     "kids_count" => $customerReports->where("age_category",   "Child")->count()
        // ];
    }
}
