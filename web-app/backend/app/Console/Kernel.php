<?php

namespace App\Console;

use App\Models\AccessControlTimeSlot;
use App\Models\Company;
use App\Models\DeviceActivesettings;
use App\Models\PayrollSetting;
use App\Models\ReportNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $monthYear = date("M-Y");

        $schedule
            ->command('task:sync_attendance_camera_logs')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule
            ->command('task:update_company_ids')
            ->everyMinute()
            ->withoutOverlapping();


        // $schedule->call(function () {
        //     exec('pm2 reload 3');
        //     info("Camera Log listener restart");
        // })->dailyAt('00:00');


        // $schedule->call(function () {
        //     exec('pm2 reload 11');
        //     info("Log listener backup restart");
        // })->monthlyOn(1, "00:00");


        $companyIds = Company::pluck("id");
        //step 1 ;

        foreach ($companyIds as $companyId) {

            $schedule
                ->command("sync_customer_report {$companyId} " . date("Y-m-d"))
                ->everyMinute()
                ->runInBackground()
                ->withoutOverlapping();

            $schedule
                ->command("task:sync_auto_shift {$companyId} " . date("Y-m-d") . " false")
                ->everyFourMinutes()
                //->everyFiveMinutes()

                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/shifts/auto/$monthYear-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command("task:sync_auto_shift {$companyId} " . date("Y-m-d") . " true")
                ->everyThirtyMinutes()
                //->everyFiveMinutes()

                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/shifts/auto/$monthYear-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));


            //if ($companyId == 1) 
            {
                $schedule
                    ->command("send_notificatin_for_offline_devices {$companyId}")
                    //  ->dailyAt('09:00')
                    ->everySixHours()
                    // ->everyThirtyMinutes()
                    // ->everyMinute()
                    // ->runInBackground()
                    //->withoutOverlapping()
                    ->appendOutputTo(storage_path("kernal_logs/$monthYear-send-notification-for-offline-devices-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
            }

            $schedule
                ->command("task:sync_multi_shift_night {$companyId} " . date("Y-m-d", strtotime("yesterday")))
                ->hourly()
                ->between('00:00', '05:59')
                ->runInBackground()
                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/shifts/multi_night/$monthYear-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            // $schedule
            //     ->command("task:sync_multi_shift {$companyId} " . date("Y-m-d"))
            //     ->everyMinute()
            //     ->between('06:00', '23:59')
            //     ->runInBackground()
            //     ->withoutOverlapping()
            //     ->appendOutputTo(storage_path("kernal_logs/shifts/multi/$monthYear-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));


            // $schedule
            //     ->command("task:sync_filo_shift {$companyId} " . date("Y-m-d"))
            //     // ->hourly()
            //     ->everyMinute()
            //     ->runInBackground()
            //     ->withoutOverlapping()
            //     ->appendOutputTo(storage_path("kernal_logs/shifts/filo/$monthYear-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            // $schedule
            //     ->command("task:sync_night_shift {$companyId} " . date("Y-m-d"))
            //     // ->hourly()
            //     ->everyMinute()
            //     ->runInBackground()
            //     ->withoutOverlapping()
            //     ->appendOutputTo(storage_path("kernal_logs/shifts/night/$monthYear-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));


            // $schedule
            //     ->command("task:sync_single_shift {$companyId} " . date("Y-m-d"))
            //     // ->hourly()
            //     ->everyMinute()
            //     ->runInBackground()
            //     ->withoutOverlapping()
            //     ->appendOutputTo(storage_path("kernal_logs/shifts/single/$monthYear-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            // $schedule
            //     ->command("task:sync_split_shift {$companyId} " . date("Y-m-d"))
            //     ->everyMinute()
            //     // ->dailyAt('09:00')
            //     ->runInBackground()
            //      ->withoutOverlapping()
            //     ->appendOutputTo(storage_path("kernal_logs/$monthYear-sync-split-logs-by-log-type-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));


            $schedule
                ->command("task:sync_visitor_attendance {$companyId} " . date("Y-m-d"))
                ->everyFiveMinutes()
                // ->dailyAt('09:00')
                //->withoutOverlapping()
                ->runInBackground()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-sync-visitor-logs-by-log-type-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));



            $schedule
                ->command("default_attendance_seeder {$companyId}")
                ->monthlyOn(1, "00:00")
                ->runInBackground()
                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-default-attendance-seeder-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            //whatsapp reports 
            $array = ['All', "P", "A", "M", "ME"];
            foreach ($array as $status) {

                $schedule
                    ->command("task:generate_daily_report {$companyId}  {$status}")

                    ->dailyAt('03:45')
                    // ->runInBackground()
                    //->withoutOverlapping()
                    ->appendOutputTo(storage_path("kernal_logs/$monthYear-generate_daily_report-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

                $schedule
                    ->command("task:generate_weekly_report {$companyId} {$status}")

                    ->dailyAt('04:00')
                    // ->runInBackground()
                    //->withoutOverlapping()
                    ->appendOutputTo(storage_path("kernal_logs/$monthYear-generate_weekly_report-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

                $schedule
                    ->command("task:generate_monthly_report {$companyId} {$status}")

                    ->monthlyOn(1, "04:30")
                    // ->runInBackground()
                    //->withoutOverlapping()
                    ->appendOutputTo(storage_path("kernal_logs/$monthYear-generate_monthly_report-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
            }

            $schedule
                ->command("task:send_whatsapp_notification {$companyId}")

                ->dailyAt('09:00')
                ->runInBackground()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-send-whatsapp-notification-{$companyId}.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command("task:sync_leaves $companyId")

                ->dailyAt('01:00')
                // ->runInBackground()
                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-leaves-$companyId.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command("task:sync_holidays $companyId")

                ->dailyAt('01:30')
                // ->runInBackground()
                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-holidays-$companyId.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command("task:sync_monthly_flexible_holidays --company_id=$companyId")

                ->dailyAt('02:00')
                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-monthly-flexible-holidays-$companyId.log"))
                ->runInBackground(); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));


            $schedule
                ->command("task:sync_off $companyId")

                ->dailyAt('02:00')
                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-sync-off-by-day-$companyId.log"))
                ->runInBackground(); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));


            $schedule
                ->command("task:sync_visitor_set_expire_dates $companyId")
                ->everyFiveMinutes()
                //->withoutOverlapping()
                ->appendOutputTo(storage_path("kernal_logs/$monthYear-visitor-set-expire-date-$companyId.log"))
                ->runInBackground(); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            // $schedule
            //     ->command("task:sync_visitor_delete_expired_dates $companyId")

            //     ->dailyAt('08:00')
            //     ->appendOutputTo(storage_path("kernal_logs/$monthYear-visitor-delete-expired-dates-$companyId.log"))
            //     ->runInBackground(); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        }



        $schedule->call(function () {
            $count = Company::where("is_offline_device_notificaiton_sent", true)->update(["is_offline_device_notificaiton_sent" => false, "offline_notification_last_sent_at" => date('Y-m-d H:i:s')]);
            info($count . "companies has been updated");
        })->dailyAt('00:00');
        //->withoutOverlapping();


        $schedule->call(function () {
            exec('chown -R www-data:www-data /var/www/mytime2cloud/backend');
            // Artisan::call('cache:clear');
            // info("Cache cleared successfully at " . date("d-M-y H:i:s"));
        })->hourly();
        // $schedule->call(function () {

        //     exec('php artisan cache:clear');
        //     //Artisan::call('cache:clear');
        //     info("Cache cleared successfully at " . date("d-M-y H:i:s"));
        // })->everyFifteenMinutes();


        $schedule
            ->command('task:check_device_health')
            ->hourly()
            ->between('7:00', '23:59')
            //->withoutOverlapping()
            ->appendOutputTo(storage_path("kernal_logs/$monthYear-devices-health.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));



        $payroll_settings = PayrollSetting::get(["id", "date", "company_id"]);

        foreach ($payroll_settings as $payroll_setting) {

            $payroll_date = (int) (new \DateTime($payroll_setting->date))->modify('-24 hours')->format('d');

            $schedule
                ->command("task:payslip_generation $payroll_setting->company_id")
                ->monthlyOn((int) $payroll_date, "00:00")
                ->appendOutputTo(storage_path("$monthYear-payslip-generate-$payroll_setting->company_id.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        }
        //whatsapp and email notifications
        $models = ReportNotification::get();

        foreach ($models as $model) {
            $scheduleCommand = $schedule->command('task:report_notification_crons ' . $model->id . ' ' . $model->company_id)
                ->runInBackground()
                ->appendOutputTo("custom_cron.log");
            //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            if ($model->frequency == "Daily") {
                $scheduleCommand->dailyAt($model->time);
            } elseif ($model->frequency == "Weekly") {
                $scheduleCommand->weeklyOn($model->day, $model->time);
            } elseif ($model->frequency == "Monthly") {
                $scheduleCommand->monthlyOn($model->day, $model->time);
            }
        }

        $schedule
            ->command('task:render_missing')
            ->dailyAt('02:15')

            //->withoutOverlapping()
            ->appendOutputTo(storage_path("kernal_logs/$monthYear-render-missing-logs.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

        if (env("APP_ENV") == "production") {
            $schedule
                ->command('task:db_backup')
                ->dailyAt('6:00')
                ->appendOutputTo(storage_path("kernal_logs/db_backup.log"))
                ->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));

            $schedule
                ->command('restart_sdk')


                ->dailyAt('4:00')
                //->hourly()
                ->appendOutputTo(storage_path("kernal_logs/restart_sdk.log")); //->emailOutputOnFailure(env("ADMIN_MAIL_RECEIVERS"));
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
