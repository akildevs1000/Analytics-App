<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class CreateFakeCustomers extends Command
{
    protected $signature = 'generate:fake-customers';
    protected $description = 'Command description';

    public function handle()
    {
        $company_id = $this->ask('Enter Company Id', 1);
        $branch_id = $this->ask('Enter Branch Id', 1);
        $count = $this->ask("How many customers you want to create? default: 10", 10);

        $overridePayload = [
            "company_id" =>  $company_id,
            "branch_id" =>  $branch_id,
        ];

        $confirmed = false;

        while (!$confirmed) {
            Customer::factory()->count($count)->create($overridePayload);
            $this->info("Data has been created");
            $this->ask('Press Enter to close.');
            $confirmed = true;
        }
    }
}
