<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\CustomerReport;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class GenerateFakeCustomerReport extends Command
{
    protected $signature = 'generate:fake-customer-report';

    protected $company_id = 2;
    protected $branch_id = 2;

    protected $description = 'Generate random data for customer_reports table';

    public function handle()
    {
        $this->company_id = $this->ask('Enter Company Id', $this->company_id);
        $this->branch_id = $this->ask('Enter Branch Id', $this->branch_id);

        $params = [
            "company_id" =>  $this->company_id,
            "branch_id" =>  $this->branch_id,
        ];

        $logs = [];

        $Customers = Customer::where($params)->get(["system_user_id", "type"])->toArray();

        foreach ($Customers as $Customer) {

            $age = mt_rand(1, 100);
            $logs[] = [
                "user_id" => $Customer["system_user_id"],
                "status_label" => mt_rand(0, 1) ? 'whitelisted' : 'blocklisted',
                "type" =>  mt_rand(0, 1) ? "vip" : "normal",
                "date" => date("Y-m-d"),
                "age_category" => $this->getAgeCategory($age),
                "status" => mt_rand(0, 1) ? 'in' : 'out',
                "gender" => mt_rand(0, 1) ? 'Male' : 'Female',
                "company_id" => $this->company_id,
                "branch_id" => $this->branch_id,
                "in_id" => 0,
                "out_id" => 0,
                "total_hrs" => Arr::random([1, 2, 3, 4, 5]),
            ];
        }

        CustomerReport::truncate();

        CustomerReport::insert($logs);

        $this->info("Record has been inserted with company id ({$this->company_id}) and branch id ({$this->branch_id})");
    }

    public function getAgeCategory($age)
    {
        return match (true) {
            $age <= 10 => 'CHILD',
            $age > 10  && $age < 25 => 'YOUNGER',
            $age >= 25 && $age < 50 => 'ADULT',
            $age >= 50   => 'SENIOR',
            default => 'Invalid Age',
        };
    }
}
