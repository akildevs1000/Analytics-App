<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\CustomerReport;
use App\Models\EmployeeLeaves;
use App\Models\Holidays;
use App\Models\Visitor;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class CustomersDashboardController extends Controller
{



    public function getDashboardWeeklyCount(Request $request)
    {



        // Get the first and last day of the month
        $firstDayOfMonth = $request->from_date; //Carbon::now()->startOfMonth()->format('Y-m-d');
        $lastDayOfMonth = $request->to_date;; // Carbon::now()->endOfMonth()->format('Y-m-d');



        //Holidays List 

        $holidays = Holidays::where("company_id",  $request->company_id)
            ->when(request()->filled("branch_id") && request("branch_id") > 0, function ($q) {
                return $q->where('branch_id', request('branch_id'));
            })->get();

        $finalHolidaysArray = [];
        foreach ($holidays as $key => $row) {
            if ($row['start_date'] == $row['end_date']) {
                $finalHolidaysArray[$row['start_date']] = $row['name'];
            } else {
                foreach ($this->generateDateRangeArray($row['start_date'], $row['end_date']) as $key => $value) {
                    $finalHolidaysArray[$value] = $row['name'];
                }
            }
        }


        //  $finalHolidaysArray = array_unique($finalHolidaysArray);

        //weekends list 

        $weekendsList = [
            "monday" => false,
            "tuesday" => false,
            "wednesday" => false,
            "thursday" => false,
            "friday" => false,
            "saturday" => false,
            "sunday" => false,
        ];
        if ($request->branch_id == 0) {
            $weekEnds = Company::where("id", $request->company_id)->get(["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]);
            $weekendsList = $weekEnds[0] ?? $weekendsList;
        } else {
            $weekEnds =   CompanyBranch::where("id", $request->branch_id)->get(["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]);
            $weekendsList = $weekEnds[0] ?? $weekendsList;
        }










        // Generate an array of all dates for the month
        $dates = [];
        $currentDate = $firstDayOfMonth;
        while ($currentDate <= $lastDayOfMonth) {
            $dates[] = $currentDate;
            $currentDate = Carbon::parse($currentDate)->addDay()->format('Y-m-d');
        }


        $distinctUserCountByDateAndHour = AttendanceLog::where("company_id", $request->company_id)
            ->selectRaw('DATE(LogTime) as date1')
            ->selectRaw('SUBSTRING(LogTime, 12, 2) AS hour')
            ->selectRaw('COUNT(DISTINCT UserID) as user_count')
            ->whereBetween('LogTime', [$firstDayOfMonth . ' 00:00:00', $lastDayOfMonth . ' 23:59:59'])
            ->when(request()->filled("branch_id"), function ($q) {
                return $q->where('branch_id', request('branch_id'));
            })
            ->groupBy(DB::raw('DATE(LogTime)'), DB::raw('SUBSTRING(LogTime, 12, 2)'))
            ->orderBy('date1')
            ->orderBy('hour')

            ->get();

        // Create an associative array with default user count as 0 for all dates and hours
        $userCountsByDateAndHour = [];
        foreach ($dates as $date) {
            foreach (range(0, 23) as $hour) {
                $userCountsByDateAndHour[$date][(int)$hour] = 0;
            }
        }

        // Fill the array with actual user counts from the query result
        foreach ($distinctUserCountByDateAndHour as $data) {
            $userCountsByDateAndHour[$data->date1][(int)$data->hour] = $data->user_count;
        }
        $finalArray = [];

        // Display the results
        foreach ($userCountsByDateAndHour as $date => $hours) {
            foreach ($hours as $hour => $user_count) {

                if ($request->day_filter == '')  // no filter specified
                    $finalArray[(int)$hour][] =  $user_count;
                else {


                    if ($this->IsfilterInWeekdaysHolidays($request->day_filter, $date, $finalHolidaysArray, $weekendsList))
                        $finalArray[(int)$hour][] =  $user_count;
                }
            }
        }


        $startIgnore = 0;
        $endIgnore = 10;
        //$rowSum0To23 = [];

        $finalArray0To10 = array_slice($finalArray,   $startIgnore, $endIgnore);
        $finalArray10To23 = array_slice($finalArray, $endIgnore + 1, 23);
        //if (isset($finalArray0To10[0]))
        $rowSum0To23 = array_fill(0, count($finalArray0To10[0]), 0); // Initialize an array to store sums

        foreach ($finalArray0To10 as $row) {
            foreach ($row as $index => $value) {
                $rowSum0To23[$index] += $value; // Sum the values at each index
            }
        }

        foreach ($finalArray0To10 as $row) {
            foreach ($row as $index => $value) {
                $rowSum0To23[$index] += $value; // Sum the values at each index
            }
        }


        array_unshift($finalArray10To23, $rowSum0To23);

        $finalArrayWithHour = [];
        foreach ($finalArray10To23 as $key => $value) {
            $finalArrayWithHour[] = ['hour' => $key, 'value' => $value];
        }

        $colorCodes = [
            ["min" => 1, "max" => 2, "color" => "#0086A8"],
            ["min" => 3, "max" => 5, "color" => "#006078"],
            ["min" => 6, "max" => 10, "color" => "#004D60"],
            ["min" => 11, "max" => 20, "color" => "#00242C"],
            ["min" => 21, "max" => 50, "color" => "#00161A"]
        ];
        //------------------------------------




        return [
            "data" => $finalArrayWithHour, "colorCodes" => $colorCodes,
            "dates" => $this->generateDateRangeArray($firstDayOfMonth, $lastDayOfMonth),
            "hours" => $this->generateClockTimingsArray($startIgnore, $endIgnore),
            "holidaysList" => ($finalHolidaysArray),
            "weekendsList" => $weekendsList
        ];
    }

    function IsfilterInWeekdaysHolidays($filter_type, $date, $finalHolidaysArray, $weekendsList)
    {

        if ($filter_type == 'holidays') {
            return isset($finalHolidaysArray[$date]) ? true : false;
        }
        if ($filter_type == 'weekends') {
            return ($weekendsList[(strtolower(date('l', strtotime($date))))] == 1) ? true : false;
        }
    }
    function generateClockTimingsArray($startIgnore, $endIgnore)
    {

        $clockTimings = array();


        // $startIgnore = 0;
        // $endIgnore = 13;

        $start = ($startIgnore % 12 == 0) ? "12AM" : ($startIgnore % 12) . "AM"; // Start time of the hour
        $end = (($endIgnore + 1) % 12 == 0) ? "12PM" : (($endIgnore + 1) % 12) . "AM"; // End time of the hour
        if ($startIgnore >= 12) {
            $start = ($startIgnore % 12 == 0) ? "12PM" : ($startIgnore % 12) . "PM";
        }
        if ($endIgnore >= 12) {

            $end = (($endIgnore + 1) % 12 == 0) ? "12AM" : (($endIgnore + 1) % 12) . "PM";
        }
        $clockTimings[] = $start . " - " . $end; // Add the timing range to the array
        // Loop through each hour from 0AM to 23PM
        for ($hour = $endIgnore + 1; $hour < 24; $hour++) {
            $start = ($hour % 12 == 0) ? "12AM" : ($hour % 12) . "AM"; // Start time of the hour
            $end = (($hour + 1) % 12 == 0) ? "12PM" : (($hour + 1) % 12) . "AM"; // End time of the hour
            if ($hour >= 12) {
                $start = ($hour % 12 == 0) ? "12PM" : ($hour % 12) . "PM";
                $end = (($hour + 1) % 12 == 0) ? "12AM" : (($hour + 1) % 12) . "PM";
            }
            $clockTimings[] = $start . " - " . $end; // Add the timing range to the array
        }

        return $clockTimings;
    }

    function generateDateRangeArray($startDate, $endDate)
    {
        // Convert string dates to DateTime objects
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // Initialize an empty array to store dates
        $datesArray = array();

        // Include the end date in the range
        $end->modify('+1 day');

        // Loop through each day from the start date to the end date
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($start, $interval, $end);
        foreach ($dateRange as $date) {
            // Format the date as 'Y-m-d' and add it to the array
            $datesArray[] = $date->format('Y-m-d');
        }

        return $datesArray;
    }
    public function getDashboardHourlyInCount(Request $request)
    {


        $date = date('Y-m-d');
        if ($request->filled("filter_from_date")) {
            $date = $request->filter_from_date;
        }
        $finalarray = [];

        for ($i = 0; $i < 24; $i++) {

            $j = $i;

            $j = $i <= 9 ? "0" . $i : $i;


            $customerReportModel = CustomerReport::with(["in_log"])
                ->where("company_id", $request->company_id)
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

            $customerReportModel->when(request()->filled("filter_duration_min"), function ($q) {
                $q->where('total_hrs', '>',  (int)request("filter_duration_min"));
            });
            $customerReportModel->when(request()->filled("filter_duration_max"), function ($q) {
                $q->where('total_hrs', '<', (int)request("filter_duration_max"));
            });




            $customerReportModel->where(function ($q) use ($date, $j) {
                $q->whereHas("in_log", function ($qu)  use ($date, $j) {
                    $qu->where('LogTime', '>=', $date . ' ' . $j . ':00:00');
                    $qu->where('LogTime', '<=', $date  . ' ' . $j . ':59:59');
                });
            });

            $customerReportTodayModel = $customerReportModel->where("date", $date);



            $maleCount = $customerReportTodayModel->clone()->where(function ($q) {
                $q->whereHas("in_log", function ($qu) {
                    $qu->where('Gender',  "Male");
                    $qu->where('age_category', '!=', 'CHILD');
                });
            })->count();
            $femaleCount = $customerReportTodayModel->clone()->where(function ($q) {
                $q->whereHas("in_log", function ($qu) {
                    $qu->where('Gender',  "Female");
                    $qu->where('age_category', '!=', 'CHILD');
                });
            })->count();

            $kidsCount = $customerReportTodayModel->clone()->where(function ($q) {
                $q->whereHas("in_log", function ($qu) {
                    $qu->where('age_category',   'CHILD');
                });
            })->count();

            $finalarray[] = [
                "date" => $date,
                "hour" => $i,
                "maleCount" => $maleCount,
                "femaleCount" => $femaleCount,
                "kidsCount" => $kidsCount,

            ];
        }

        return [

            "houry_data" => $finalarray
        ];

        // $date = date('Y-m-d');
        // if ($request->filled("from_date")) {
        //     $date = $request->from_date;
        // }
        // $finalarray = [];

        // for ($i = 0; $i < 24; $i++) {

        //     $j = $i;

        //     $j = $i <= 9 ? "0" . $i : $i;



        //     $AttendanceLogModel = AttendanceLog::with(["customer"])->where("company_id", $request->company_id)
        //         ->when(request()->filled("branch_id"), function ($q) {
        //             $q->where('branch_id', request("branch_id"));
        //         })
        //         ->where('LogTime', '>=', $date . ' ' . $j . ':00:00')
        //         ->where('LogTime', '<=', $date  . ' ' . $j . ':59:59');


        //     $AttendanceLogModel->when(request()->filled("filter_duration"), function ($q) {

        //         $q->whereHas("customer", function ($qu) {
        //             $qu->where('total_hrs', "<=", request("filter_duration"));
        //             $qu->where('total_hrs', request("filter_duration"));
        //         });
        //     });
        //     // $AttendanceLogModel->whereHas("customer", function ($qu) {
        //     //     $qu->where('status',  "out");
        //     // });

        //     return $AttendanceLogModel->get();
        //     $maleCount = $AttendanceLogModel->clone()->where('Gender',  "Male")->where('age_category', '!=', 'CHILD')->distinct('UserID')->count();
        //     $femaleCount = $AttendanceLogModel->clone()->where('Gender',  "Female")->where('age_category', '!=', 'CHILD')->distinct('UserID')->count();
        //     $kidsCount = $AttendanceLogModel->clone()->where('age_category',  'CHILD')->distinct('UserID')->count();



        //     $finalarray[] = [
        //         "date" => $date,
        //         "hour" => $i,
        //         "maleCount" => $maleCount,
        //         "femaleCount" => $femaleCount,
        //         "kidsCount" => $kidsCount,
        //     ];
        // }

        // return [

        //     "houry_data" => $finalarray
        // ];
    }
    public function getDashboardHourlyOutCount(Request $request)
    {


        $date = date('Y-m-d');
        if ($request->filled("filter_from_date")) {
            $date = $request->filter_from_date;
        }
        $finalarray = [];

        for ($i = 0; $i < 24; $i++) {

            $j = $i;

            $j = $i <= 9 ? "0" . $i : $i;

            $customerReportModel = CustomerReport::with(["out_log"])
                ->where("company_id", $request->company_id)
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

            $customerReportModel->where(function ($q) use ($date, $j) {
                $q->whereHas("out_log", function ($qu)  use ($date, $j) {
                    $qu->where('LogTime', '>=', $date . ' ' . $j . ':00:00');
                    $qu->where('LogTime', '<=', $date  . ' ' . $j . ':59:59');
                });
            });

            $customerReportTodayModel = $customerReportModel->where("date", $date);

            $maleCount = $customerReportTodayModel->clone()->where(function ($q) {
                $q->whereHas("out_log", function ($qu) {
                    $qu->where('Gender',  "Male");
                    $qu->where('age_category', '!=', 'CHILD');
                });
            })->count();
            $femaleCount = $customerReportTodayModel->clone()->where(function ($q) {
                $q->whereHas("out_log", function ($qu) {
                    $qu->where('Gender',  "Female");
                    $qu->where('age_category', '!=', 'CHILD');
                });
            })->count();

            $kidsCount = $customerReportTodayModel->clone()->where(function ($q) {
                $q->whereHas("out_log", function ($qu) {
                    $qu->where('age_category',   'CHILD');
                });
            })->count();

            $finalarray[] = [
                "date" => $date,
                "hour" => $i,
                "maleCount" => $maleCount,
                "femaleCount" => $femaleCount,
                "kidsCount" => $kidsCount,

            ];
        }

        return [

            "houry_data" => $finalarray
        ];
    }

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

        $room_occupancy_percentage = 0;

        $room_occupancy_before1day_percentage = 0;

        $total_footfall_yesterday_percentage = 0;


        $total_company_capacity_occupancy = 0;
        $date = $request->filter_from_date;


        $total_company_capacity_occupancy = CompanyBranch::where("company_id", $request->company_id)


            ->when(request()->filled("branch_id"), function ($q) {

                $q->where('id', request("branch_id"));
            })->sum("occupancy");



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
            $room_occupancy_percentage = round($total_footfall_count * 100 / $total_company_capacity_occupancy, 0);


            if ($total_footfall_count_before1day == 0) {
            } else {
                $room_occupancy_before1day_percentage = round($total_footfall_count * 100 / $total_footfall_count_before1day, 0);
            }

            if ($total_footfall_count_before1day >  $total_footfall_count) {
                $room_occupancy_before1day_percentage = '-' . $room_occupancy_before1day_percentage;
            }
        }




        //total_hrs
        // $totalHrsArray = $customerReportTodayModel->clone()->selectRaw('avg(total_hrs) as avg_total_hrs, min(total_hrs) as min_total_hrs, max(total_hrs) as max_total_hrs')
        //     ->first();

        $totalHrsArray = $customerReportTodayModel
            ->clone()
            ->selectRaw('AVG(CAST(total_hrs AS FLOAT)) AS avg_total_hrs, MIN(CAST(total_hrs AS FLOAT)) AS min_total_hrs, MAX(CAST(total_hrs AS FLOAT)) AS max_total_hrs')
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
            "room_occupancy_percentage" =>  $room_occupancy_percentage,
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
