<?php

namespace App\Console\Commands;

use App\Http\Controllers\AttendanceLogCameraController;
use App\Models\Community\AttendanceLog;
use App\Models\Customer;
use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class GenerateRandomData extends Command
{
    protected $signature = 'generate:fake-logs';

    protected $company_id = 1;
    protected $branch_id = 1;

    protected $description = 'Generate random data for attendance_logs table';

    public function handle()
    {
        $this->company_id = $this->ask('Enter Company Id', $this->company_id);
        $this->branch_id = $this->ask('Enter Branch Id', $this->branch_id);
        $sleep = $this->ask('Enter Sleep Time in numbers e.g. 1,2,3,4,5', 5);



        $params = [
            "company_id" =>  $this->company_id,
            "branch_id" =>  $this->branch_id,
        ];

        $UserIDs = Customer::where($params)->pluck("system_user_id")->toArray();
        $DeviceIDs = Device::where($params)->pluck("device_id")->toArray();

        $seenValues = [];

        while (true) {

            $age = mt_rand(1, 100);
            $log = [
                "UserID" => Arr::random($UserIDs),
                "DeviceID" => Arr::random($DeviceIDs),
                "LogTime" => now()->toDateTimeString(),
                "SerialNumber" => mt_rand(1, 100),
                "FaceID" => mt_rand(1, 200),
                "Clarity" => number_format(mt_rand() / mt_getrandmax(), 6),
                "Age" => $age,
                "age_category" => (new AttendanceLogCameraController)->getAgeCategory($age),
                "Quality" => number_format(mt_rand() / mt_getrandmax(), 6),
                "Gender" => mt_rand(0, 1) ? 'Male' : 'Female',
                "Similarity" => number_format(mt_rand() / mt_getrandmax(), 6),
                "user_type" => "Customer",
                "company_id" => $this->company_id,
                "branch_id" => $this->branch_id,
            ];
            if (!in_array($log, $seenValues)) {
                $seenValues[] = $log;
                AttendanceLog::create($log);
                $this->info("Record has been inserted with company id ({$this->company_id}) and branch id ({$this->branch_id})");
            }
            sleep($sleep); 
        }
    }
}
