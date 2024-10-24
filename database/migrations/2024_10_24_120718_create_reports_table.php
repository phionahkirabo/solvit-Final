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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id'); // Creates an auto-incrementing ID column
            $table->string('title'); // Title of the report
            $table->text('content'); // Content of the report
            $table->unsignedBigInteger('task_id'); // Task ID, not nullable
            $table->unsignedBigInteger('employee_id')->nullable(); // Employee ID, nullable
            $table->unsignedBigInteger('hod_id')->nullable(); // HOD ID, nullable
            $table->timestamps(); // Creates created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('task_id')->references('task_id')->on('tasks')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('hod_id')->references('id')->on('hods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
        public function down()
        {
            Schema::dropIfExists('reports');
        }
};
