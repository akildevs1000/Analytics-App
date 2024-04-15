<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('branch_shift_managers', function (Blueprint $table) {
            $table->id();
            $table->integer("company_id")->nullable();
            $table->integer("branch_id")->nullable();
            $table->integer("employees_table_id")->nullable();
            $table->integer("shift_start")->nullable();
            $table->integer("shift_end")->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_shift_managers');
    }
};
