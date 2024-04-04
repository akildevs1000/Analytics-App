<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ServeWithIp extends Command
{
    protected $signature = 'serve:init';

    protected $description = 'Start the Laravel app with a specific IP address and port no.';

    public function handle()
    {
        $localIp = gethostbyname(gethostname());


        $ip = $this->ask('Enter IP address.', $localIp);
        $port = $this->ask('Enter port no.', 8000);


        // Validate IP address
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error('Invalid IP address format.');
            return;
        }
        
        Artisan::call('serve', [
            '--host' => $ip,
            '--port' => $port,
        ], $this->output);
    }
}
