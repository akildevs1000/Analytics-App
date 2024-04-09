<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            // $table->dateTime("LogTime")->nullable()->change();


            //$table->timestamp('LogTime')->nullable()->change();
        });

        // Specify the conversion
        // DB::statement('ALTER TABLE attendance_logs ALTER COLUMN "LogTime" TYPE timestamp without time zone USING "LogTime"::timestamp without time zone');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->timestamp('LogTime')->nullable()->change();
        });
    }
};
