<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCommaSeperatedColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('issues','tag_id','people_involved_id'))
        {
        Schema::table('issues', function ($table) {
            $table->dropColumn('tag_id');     
            $table->dropColumn('people_involved_id');     
         });
        }
        if (Schema::hasColumn('issue_steps','assigned_to')) {
         Schema::table('issue_steps', function ($table) {
            $table->dropColumn('assigned_to');     
         });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
