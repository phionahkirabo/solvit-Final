<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('task_id');  
            $table->string('task_name');  
            $table->text('description');  
            $table->date('start_date');  
            $table->date('due_date');  
            $table->enum('status', ['Pending', 'In Progress', 'Completed']);  

            // Define foreign keys
            $table->unsignedBigInteger('project_id');  
            $table->unsignedBigInteger('employee_id'); 
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        
            $table->timestamps();  
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
