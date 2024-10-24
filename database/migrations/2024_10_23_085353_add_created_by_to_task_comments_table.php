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
            $table->unsignedBigInteger('created_by')->after('task_id'); // Add created_by column
            $table->string('user_type')->after('created_by'); // Add user_type column ('hod' or 'employee')
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
            $table->dropColumn('created_by');
            $table->dropColumn('user_type');
        });
    }
};