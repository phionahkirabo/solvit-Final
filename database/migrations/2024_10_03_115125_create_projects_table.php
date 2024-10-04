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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id'); // Unique identifier
            $table->string('project_name'); // Project name
            $table->text('description')->nullable(); // Brief description
            $table->date('start_date'); // Project start date
            $table->date('end_date')->nullable(); // Project end or expected completion date
            $table->enum('status', ['Active', 'Completed', 'On Hold', 'Cancelled', 'Pending'])->default('Pending'); // Project status
            $table->unsignedBigInteger('hod_id'); // Foreign key to hods table
            $table->string('project_category'); // Project category
            $table->timestamps(); // created_at and updated_at timestamps

            // Foreign key constraint
            $table->foreign('hod_id')->references('id')->on('hods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
