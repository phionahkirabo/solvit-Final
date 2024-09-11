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
       Schema::create('hods', function (Blueprint $table) {
            $table->id('id');
            $table->string('hod_name');
            $table->string('email')->unique();
            $table->string('contact_number');
            $table->string('password'); // Store hashed passwords
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
        Schema::dropIfExists('hods');
    }
};
