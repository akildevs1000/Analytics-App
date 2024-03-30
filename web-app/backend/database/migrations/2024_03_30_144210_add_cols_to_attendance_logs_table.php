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
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->string("FaceID")->nullable();
            $table->string("Clarity")->nullable();
            $table->string("Age")->nullable();
            $table->string("Quality")->nullable();
            $table->string("Gender")->nullable();
            $table->string("Similarity")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn('FaceID');
            $table->dropColumn('Clarity');
            $table->dropColumn('Age');
            $table->dropColumn('Quality');
            $table->dropColumn('Gender');
            $table->dropColumn('Similarity');
        });
    }
};
