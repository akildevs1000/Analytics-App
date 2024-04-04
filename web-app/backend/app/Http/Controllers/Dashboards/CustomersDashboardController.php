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
use Illuminate\Database\Eloquent\Builder;

class CustomersDashboardController extends Controller
{


    public function dashboardStatistics(Request $request)
    {


        $live_total_count = 0;
        $live_female_count = 0;
        $live_child_count = 0;
        $out_total_count = 0;
        $out_male_count = 0;
        $out_female_count = 0;
        $out_child_count = 0;
        $total_footfall_count = 0;
        $total_footfall_count_before1day = 0;
        $room_occupancy = 0;
        $avg_time_spent = 0;
        $min_time_spent = 0;
        $max_time_spent = 0;
        $total_male_count = 0;
        $total_female_count = 0;
        $total_child_count = 0;
        $total_male_count_before1day = 0;
        $total_female_count_before1day = 0;
        $total_child_count_before1day = 0;
        $male_younger_count = 0;
        $male_adult_count = 0;
        $male_senior_count = 0;
        $female_younger_count = 0;
        $female_adult_count = 0;
        $female_senior_count = 0;
        $child_male_count = 0;
        $child_female_count = 0;
        $vip_customer_count = 0;
        $repeated_customer_count = 0;
        $blocked_customer_count = 0;

        $total_footfall_yesterday_percentage = 0;


        $total_company_capacity_occupancy = 10;
        $date = $request->date;


        $AttendanceLogModel = AttendanceLog::where("company_id", $request->company_id)

            ->where("date", $date)
            ->when(request()->filled("branch_id"), function ($q) {

                $q->where('branch_id', request("branch_id"));
            })->when(request()->filled("DeviceID"), function ($q) {

                $q->where('DeviceID', request("DeviceID"));
            });



        $customerReportModel = CustomerReport::with(["in_log", "out_log",  "company", "in_log", "customer"])->where("company_id", $request->company_id)
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
        if ($total_footfall_count_before1day > 0)
            $total_footfall_yesterday_percentage = round($total_footfall_count * 100 / $total_footfall_count_before1day, 0);
        else
            $total_footfall_yesterday_percentage = "100" . '+';

        if ($total_footfall_count_before1day > $total_footfall_count) {
            $total_footfall_yesterday_percentage = '-' . $total_footfall_yesterday_percentage;
        }

        //Customer Type

        $vip_customer_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("customer", function ($qu) {
                $qu->where('type',  "vip");
            });
        })->count();
        $blocked_customer_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("customer", function ($qu) {
                $qu->where('status',  "blocklisted");
            });
        })->count();
        $repeated_customer_count = $AttendanceLogModel->groupBy('UserID')
            ->havingRaw('COUNT(*) > 2')
            ->count();

        // //Yesterday Male/Femlae/Kids 
        $total_male_count_before1day = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category', '!=', 'CHILD');
            });
        })->count();

        $total_female_count_before1day = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category', '!=', 'CHILD');
            });
        })->count();
        $total_child_count_before1day = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {

                $qu->where('age_category',  'CHILD');
            });
        })->count();

        $total_footfall_yesterday = $total_male_count_before1day + $total_female_count_before1day + $total_child_count_before1day;
        //Today MALE 
        $male_younger_count = $customerReportTodayModel->clone()->with(['in_log'])
            ->whereHas('in_log', function ($query) {
                $query->where('Gender', 'Male')
                    ->where('age_category', 'YOUNGER');
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

        //Yesterday MALE 
        $male_younger_count_yesterday = $customerReportYesterdayModel->clone()->with(['in_log'])
            ->whereHas('in_log', function ($query) {
                $query->where('Gender', 'Male')
                    ->where('age_category', 'YOUNGER');
            })->count();
        $male_adult_count_yesterday  = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'ADULT');
            });
        })->count();
        $male_senior_count_yesterday  = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'SENIOR');
            });
        })->count();

        $total_male_count_yesterday = $male_younger_count_yesterday + $male_adult_count_yesterday + $male_senior_count_yesterday;




        //Today FEMALE 
        $female_younger_count = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'YOUNGER');
            });
        })->count();

        $female_adult_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'ADULT');
            });
        })->count();
        $female_senior_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'SENIOR');
            });
        })->count();

        $total_female_count = $female_younger_count + $female_adult_count + $female_senior_count;

        //yeserday FEMALE 
        $female_younger_count_yesterday = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'YOUNGER');
            });
        })->count();

        $female_adult_count_yesterday  = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'ADULT');
            });
        })->count();
        $female_senior_count_yesterday  = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'SENIOR');
            });
        })->count();

        $total_female_count_yesterday = $female_younger_count_yesterday + $female_adult_count_yesterday + $female_senior_count_yesterday;




        //Today KIDS 
        $child_male_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'CHILD');
            });
        })->count();

        $child_female_count  = $customerReportTodayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'CHILD');
            });
        })->count();

        $total_child_count = $child_male_count + $child_female_count;


        //Yesterday KIDS 
        $child_male_count_yesterday  = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category',  'CHILD');
            });
        })->count();

        $child_female_count_yesterday = $customerReportYesterdayModel->clone()->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category',  'CHILD');
            });
        })->count();

        $total_child_count_yesterday = $child_male_count_yesterday + $child_female_count_yesterday;




        //occupancy
        if ($total_company_capacity_occupancy > 0) {
            $room_occupancy = round($total_footfall_count * 100 / $total_company_capacity_occupancy, 0);


            if ($total_footfall_count_before1day == 0) {
            } else {
                $room_occupancy_before1day_percentage = round($total_footfall_count * 100 / $total_footfall_count_before1day, 0);
            }

            if ($total_footfall_count_before1day >  $total_footfall_count) {
                $room_occupancy_before1day_percentage = '-' . $room_occupancy_before1day_percentage;
            }
        }




        //total_hrs
        $totalHrsArray = $customerReportTodayModel->clone()->selectRaw('avg(total_hrs) as avg_total_hrs, min(total_hrs) as min_total_hrs, max(total_hrs) as max_total_hrs')
            ->first();

        $avg_time_spent = $totalHrsArray->avg_total_hrs;
        $min_time_spent = $totalHrsArray->min_total_hrs;
        $max_time_spent = $totalHrsArray->max_total_hrs;



        //OUT
        $out_total_count = $customerReportTodayModel->clone()->where("status", "out")->count();

        $out_male_count = $customerReportTodayModel->clone()->where("status", "out")->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Male");
                $qu->where('age_category', '!=',  'CHILD');
            });
        })->count();
        $out_female_count = $customerReportTodayModel->clone()->where("status", "out")->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('Gender',  "Female");
                $qu->where('age_category', '!=',  'CHILD');
            });
        })->count();
        $out_child_count = $customerReportTodayModel->clone()->where("status", "out")->where(function ($q) {
            $q->whereHas("in_log", function ($qu) {
                $qu->where('age_category',  'CHILD');
            });
        })->count();


        //LIVE or IN 
        $live_total_count = $total_footfall_count - $out_total_count;
        $live_male_count = $total_male_count - $out_male_count;
        $live_female_count = $total_female_count - $out_female_count;
        $live_child_count = $total_child_count - $out_child_count;

        if ($total_footfall_count > 0) {
            $male_percentage = round($total_male_count * 100 / $total_footfall_count, 2);
            $female_percentage = round($total_female_count * 100 / $total_footfall_count, 2);
            $child_percentage = round($total_child_count * 100 / $total_footfall_count, 2);
        } else {
            $male_percentage = 0;
            $female_percentage = 0;
            $child_percentage = 0;
        }


        $male_percentage_yesterday = $total_male_count_before1day > 0 ? round($total_male_count * 100 / $total_male_count_before1day, 2) : '100';
        $female_percentage_yesterday =  $total_female_count_before1day > 0 ? round($total_female_count * 100 / $total_female_count_before1day, 2) : '100';
        $child_percentage_yesterday = $total_child_count_before1day > 0 ? round($total_child_count * 100 / $total_child_count_before1day, 2) : '100';


        $male_percentage_yesterday = $total_male_count_before1day > $total_male_count ? -$male_percentage_yesterday : $male_percentage_yesterday;
        $female_percentage_yesterday = $total_male_count_before1day > $total_female_count ? -$female_percentage_yesterday : $female_percentage_yesterday;

        $child_percentage_yesterday = $total_child_count_before1day > $total_child_count ? -$child_percentage_yesterday : $child_percentage_yesterday;




        $male_younger_percentage_yesterday = $male_younger_count_yesterday > 0 ? round($male_younger_count * 100 / $male_younger_count_yesterday, 2) : '100';
        $male_seniour_percentage_yesterday = $male_senior_count_yesterday > 0 ? round($male_senior_count * 100 / $male_senior_count_yesterday, 2) : '100';
        $male_adult_percentage_yesterday = $male_adult_count_yesterday > 0 ? round($male_adult_count * 100 / $male_adult_count_yesterday, 2) : '100';


        $female_younger_percentage_yesterday = $female_younger_count_yesterday > 0 ? round($female_younger_count * 100 / $female_younger_count_yesterday, 2) : '100';
        $female_seniour_percentage_yesterday = $female_senior_count_yesterday > 0 ? round($female_senior_count * 100 / $female_senior_count_yesterday, 2) : '100';
        $female_adult_percentage_yesterday = $female_adult_count_yesterday > 0 ? round($female_adult_count * 100 / $female_adult_count_yesterday, 2) : '100';


        $child_male_percentage_yesterday = $child_male_count_yesterday > 0 ? round($child_male_count  * 100 / $child_male_count_yesterday, 2) : '100';
        $child_female_percentage_yesterday = $child_female_count_yesterday > 0 ? round($child_female_count  * 100 / $child_female_count_yesterday, 2) : '100';


        $return = [

            //percentage
            "male_percentage" => $male_percentage,
            "female_percentage" =>  $female_percentage,
            "child_percentage" =>  $child_percentage,

            //percentage
            "male_percentage_yesterday" => $male_percentage_yesterday,
            "female_percentage_yesterday" =>  $female_percentage_yesterday,
            "child_percentage_yesterday" =>  $child_percentage_yesterday,

            //percentage
            "male_younger_percentage_yesterday" => $male_younger_percentage_yesterday,
            "male_seniour_percentage_yesterday" =>  $male_seniour_percentage_yesterday,
            "male_adult_percentage_yesterday" =>  $male_adult_percentage_yesterday,

            //percentage
            "female_younger_percentage_yesterday" => $female_younger_percentage_yesterday,
            "female_seniour_percentage_yesterday" =>  $female_seniour_percentage_yesterday,
            "female_adult_percentage_yesterday" =>  $female_adult_percentage_yesterday,
            //percentage
            "child_male_percentage_yesterday" => $child_male_percentage_yesterday,
            "child_female_percentage_yesterday" =>  $child_female_percentage_yesterday,


            //LIVE OR IN 
            "live_total_count" => $live_total_count,
            "live_male_count" =>  $live_male_count,
            "live_female_count" =>  $live_female_count,
            "live_child_count" =>  $live_child_count,
            //OUT
            "out_total_count" =>  $out_total_count,
            "out_male_count" =>  $out_male_count,
            "out_female_count" =>  $out_female_count,
            "out_child_count" =>  $out_child_count,


            //TOTAL 
            "total_footfall_count" =>  $total_footfall_count,
            "total_footfall_count_before1day" =>  $total_footfall_count_before1day,

            "total_footfall_yesterday_percentage" => $total_footfall_yesterday_percentage,

            "room_occupancy" =>  $room_occupancy,
            "room_occupancy_before1day_percentage" =>  $room_occupancy_before1day_percentage,




            "avg_time_spent" =>  $avg_time_spent,
            "min_time_spent" =>  $min_time_spent,
            "max_time_spent" =>  $max_time_spent,


            // //DEMOGRAPHICS 
            "total_male_count" =>  $total_male_count,
            "total_female_count" =>  $total_female_count,
            "total_child_count" =>  $total_child_count,
            "total_male_count_before1day" =>  $total_male_count_before1day,
            "total_female_count_before1day" =>  $total_female_count_before1day,
            "total_child_count_before1day" =>  $total_child_count_before1day,
            "male_younger_count" =>  $male_younger_count,
            "male_adult_count" =>  $male_adult_count,
            "male_senior_count" =>  $male_senior_count,

            "female_younger_count" =>  $female_younger_count,
            "female_adult_count" =>  $female_adult_count,
            "female_senior_count" =>  $female_senior_count,

            "child_male_count" =>  $child_male_count,
            "child_female_count" =>  $child_female_count,

            //footer 
            "vip_customer_count" =>  $vip_customer_count,
            "repeated_customer_count" =>  $repeated_customer_count,
            "blocked_customer_count" =>  $blocked_customer_count,


        ];

        return $return;
    }
}
