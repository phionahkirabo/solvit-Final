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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')-> unique();
            $table->string('phone_number');
            $table->enum('gender',['male','female']);
            $table->string('employment_status');
            $table->string('marital_status');
            $table->date('start_date');
            $table->string('qualification_level');
            $table->date('date_of_birth');
            $table->foreignId('country_id')->constrained('countries','id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users','id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('dep_id')->constrained('departments','id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('job_titles','id')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('employees');
    }
};
