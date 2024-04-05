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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string("full_name")->default();
            $table->string("first_name")->default();
            $table->string("last_name")->default();
            $table->string("profile_picture")->default();
            $table->string("system_user_id")->default();
            $table->string("type")->default();
            $table->date("date");
            $table->integer('company_id')->default(0);
            $table->integer('branch_id')->default(0);
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
        Schema::dropIfExists('customers');
    }
};
