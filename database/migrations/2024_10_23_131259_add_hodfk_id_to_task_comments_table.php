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
        Schema::table('task_comments', function (Blueprint $table) {
            // Add nullable foreign key for Hod
            $table->unsignedBigInteger('hod_id')->nullable()->after('task_id');
            $table->foreign('hod_id')->references('id')->on('hods')->onDelete('set null');

            // Add nullable foreign key for Employee
            $table->unsignedBigInteger('employee_id')->nullable()->after('hod_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_comments', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['hod_id']);
            $table->dropForeign(['employee_id']);

            // Then drop the columns
            $table->dropColumn(['hod_id', 'employee_id']);
        });
    }
};
