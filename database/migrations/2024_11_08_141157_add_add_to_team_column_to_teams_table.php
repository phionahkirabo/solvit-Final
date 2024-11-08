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
        Schema::table('teams', function (Blueprint $table) {
            $table->enum('add_to_team', [
                'Data Science and Information Systems',
                'Cybersecurity',
                'Project Management',
                'Research',
                'MEAL',
                'Marketing',
                'Sales and Revenue',
                'AI'
            ])->after('gender');  // Position this column after 'gender'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('add_to_team');
        });
    }
};
