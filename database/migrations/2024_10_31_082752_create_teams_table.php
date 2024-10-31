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
        Schema::create('teams', function (Blueprint $table) {
            $table->id('team_id');
            $table->unsignedBigInteger('hod_id');  // Foreign key for HOD
            $table->unsignedBigInteger('employee_id');  // Foreign key for Employee
            $table->string('profile_picture')->nullable();  // Path to profile picture
            $table->string('full_name');
            $table->string('id_number');
            $table->string('nationality');
            $table->string('email')->unique();
            $table->enum('gender', ['Male', 'Female', 'Other']);

            // Foreign key constraints
            $table->foreign('hod_id')->references('id')->on('hods')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            
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
        Schema::dropIfExists('teams');
    }
};
