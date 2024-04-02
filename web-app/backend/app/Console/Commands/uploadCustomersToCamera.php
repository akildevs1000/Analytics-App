<?php

namespace App\Console\Commands;

use App\Http\Controllers\SDKController;
use Illuminate\Console\Command;

class uploadCustomersToCamera extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:customers_to_camera';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = (new SDKController)->uploadCustomersToCamera();

        echo json_encode($response);
    }
}
