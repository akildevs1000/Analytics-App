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
        Schema::table('company_branches', function (Blueprint $table) {
            $table->integer("monday")->default(0);
            $table->integer("tuesday")->default(0);
            $table->integer("wednesday")->default(0);
            $table->integer("thursday")->default(0);
            $table->integer("friday")->default(0);
            $table->integer("saturday")->default(0);
            $table->integer("sunday")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_branches', function (Blueprint $table) {
            $table->dropColumn("monday");
            $table->dropColumn("tuesday");
            $table->dropColumn("wednesday");
            $table->dropColumn("thursday");
            $table->dropColumn("friday");
            $table->dropColumn("saturday");
            $table->dropColumn("sunday");
        });
    }
};
