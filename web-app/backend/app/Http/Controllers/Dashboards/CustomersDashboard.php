<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\CustomerReport;
use App\Models\EmployeeLeaves;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CustomersDashboard extends Controller
{
    public function dashboardStatistics(Request $request)
    {




        $live_total_count = 0;
        $live_female_count = 0;
        $live_kids_count = 0;
        $out_total_count = 0;
        $out_male_count = 0;
        $out_female_count = 0;
        $out_kids_count = 0;
        $total_footfall_count = 0;
        $total_footfall_count_before1day = 0;
        $room_occupancy = 0;
        $avg_time_spent = 0;
        $min_time_spent = 0;
        $max_time_spent = 0;
        $total_male_count = 0;
        $total_female_count = 0;
        $total_kids_count = 0;
        $total_male_count_before1day = 0;
        $total_female_count_before1day = 0;
        $total_kids_count_before1day = 0;
        $male_younger_count = 0;
        $male_adult_count = 0;
        $male_senior_count = 0;
        $female_younger_count = 0;
        $female_adult_count = 0;
        $female_senior_count = 0;
        $kids_male_count = 0;
        $kids_female_count = 0;
        $vip_customer_count = 0;
        $repeated_customer_count = 0;
        $blocked_customer_count = 0;

        $total_company_capacity_occupancy = 1000;
        $date = $request->date;


        $AttendanceLogModel = AttendanceLog::where("company_id", $request->company_id)

            ->where("date", $date)
            ->when(request()->filled("branch_id"), function ($q) {

                $q->where('branch_id', request("branch_id"));
            })->when(request()->filled("DeviceID"), function ($q) {

                $q->where('DeviceID', request("DeviceID"));
            });



        $customerReportModel = CustomerReport::with(["in_log", "out_log",  "company", "customer"])->where("company_id", $request->company_id)
            ->when(request()->filled("branch_id"), function ($q) {

                $q->where('branch_id', request("branch_id"));
            });

        $customerReportModel->when(request()->filled("DeviceID"), function ($query) {
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


        $customerReportTodayModel = $customerReportModel->clone()->where("date", $date);
        $customerReportYesterdayModel = $customerReportModel->clone()->where("date", date('Y-m-d', strtotime('-1 day', strtotime($date))));



        //Total footfall
        $total_footfall_count = $customerReportTodayModel->clone()->count();
        $total_footfall_count_before1day = $customerReportYesterdayModel->clone()->count();


        //Customer Type

        $vip_customer_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("customer", function ($qu) {
                $qu->where('type',  "vip");
            });
        })->count();
        $blocked_customer_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("customer", function ($qu) {
                $qu->where('type',  "blocked");
            });
        })->count();
        $repeated_customer_count = $AttendanceLogModel->groupBy('UserID')
            ->havingRaw('COUNT(*) > 2')
            ->count();

        //Yesterday Male/Femlae/Kids 
        $total_male_count_before1day = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category', '!=', 'CHILD');
            });
        })->count();

        $total_female_count_before1day = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category', '!=', 'CHILD');
            });
        })->count();
        $total_kids_count_before1day = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category', '!=', 'CHILD');
            });
        })->count();


        //Today MALE 
        $male_younger_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'YOUNGER');
            });
        })->count();

        $male_adult_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'ADULT');
            });
        })->count();
        $male_senior_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'SENIOR');
            });
        })->count();

        $total_male_count = $male_younger_count + $male_adult_count + $male_senior_count;




        //Today FEMALE 
        $female_younger_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "FeMale");
                $qu->where('age_category',  'YOUNGER');
            });
        })->count();

        $female_adult_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "FeMale");
                $qu->where('age_category',  'ADULT');
            });
        })->count();
        $female_senior_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "FeMale");
                $qu->where('age_category',  'SENIOR');
            });
        })->count();

        $total_female_count = $female_younger_count + $female_adult_count + $female_senior_count;


        //Today KIDS 
        $kids_male_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'CHILD');
            });
        })->count();

        $kids_female_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "FeMale");
                $qu->where('age_category',  'CHILD');
            });
        })->count();

        $total_kids_count = $kids_male_count + $kids_female_count;



        //occupancy
        $room_occupancy = round($total_footfall_count / $total_company_capacity_occupancy, 0);
        $room_occupancy_before1day = round($total_footfall_count_before1day / $total_company_capacity_occupancy, 0);



        //total_hrs
        $totalHrsArray = $customerReportTodayModel->clone()->selectRaw('avg(total_hrs) as avg_total_hrs, min(total_hrs) as min_total_hrs, max(total_hrs) as max_total_hrs')
            ->first();

        $avg_time_spent = $totalHrsArray->avg_total_hrs;
        $min_time_spent = $totalHrsArray->min_total_hrs;
        $max_time_spent = $totalHrsArray->max_total_hrs;



        //OUT
        $out_total_count = $customerReportTodayModel->clone()->where("status", "out")->count();

        $out_male_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("out_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category', '!=',  'CHILD');
            });
        })->count();
        $out_female_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("out_log", function ($qu) {
                $qu->where('Gender',  "FeMale");
                $qu->where('age_category', '!=',  'CHILD');
            });
        })->count();
        $out_kids_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("out_log", function ($qu) {
                $qu->where('age_category',  'CHILD');
            });
        })->count();


        //LIVE or IN 
        $live_total_count = $total_footfall_count - $out_total_count;
        $live_male_count = $total_male_count - $out_male_count;
        $live_female_count = $total_female_count - $out_female_count;
        $live_kids_count = $total_kids_count - $out_kids_count;


        $return = [
            //LIVE OR IN 
            "live_total_count" => $live_total_count,
            "live_male_count" =>  $live_male_count,
            "live_female_count" =>  $live_female_count,
            "live_kids_count" =>  $live_kids_count,
            //OUT
            "out_total_count" =>  $out_total_count,
            "out_male_count" =>  $out_male_count,
            "out_female_count" =>  $out_female_count,
            "out_kids_count" =>  $out_kids_count,


            //TOTAL 
            "total_footfall_count" =>  $total_footfall_count,
            "total_footfall_count_before1day" =>  $total_footfall_count_before1day,

            "room_occupancy" =>  $room_occupancy,
            "room_occupancy_before1day" =>  $room_occupancy_before1day,

            "avg_time_spent" =>  $avg_time_spent,
            "min_time_spent" =>  $min_time_spent,
            "max_time_spent" =>  $max_time_spent,


            //DEMOGRAPHICS 
            "total_male_count" =>  $total_male_count,
            "total_female_count" =>  $total_female_count,
            "total_kids_count" =>  $total_kids_count,
            "total_male_count_before1day" =>  $total_male_count_before1day,
            "total_female_count_before1day" =>  $total_female_count_before1day,
            "total_kids_count_before1day" =>  $total_kids_count_before1day,
            "male_younger_count" =>  $male_younger_count,
            "male_adult_count" =>  $male_adult_count,
            "male_senior_count" =>  $male_senior_count,

            "female_younger_count" =>  $female_younger_count,
            "female_adult_count" =>  $female_adult_count,
            "female_senior_count" =>  $female_senior_count,

            "kids_male_count" =>  $kids_male_count,
            "kids_female_count" =>  $kids_female_count,
            "vip_customer_count" =>  $vip_customer_count,

            "repeated_customer_count" =>  $repeated_customer_count,
            "blocked_customer_count" =>  $blocked_customer_count,


        ];

        return $return;
    }
}
