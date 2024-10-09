<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('task_id'); // Primary key
            $table->string('task_name');
            $table->text('description');
            $table->date('start_date');
            $table->date('due_date');
            $table->enum('status', ['Pending', 'In Progress', 'Completed']);
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
  