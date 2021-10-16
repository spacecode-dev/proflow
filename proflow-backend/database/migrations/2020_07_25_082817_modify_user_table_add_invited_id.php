<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUserTableAddInvitedId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
       Schema::table('users', function ($table) {
            $table->tinyInteger('is_issue_invited')->default(0);
       });

       Schema::table('issues', function ($table) {
        $table->tinyInteger('is_archived')->default(0);
        $table->dropColumn('people_invited_id');    
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('users', function ($table) {
            $table->dropColumn('is_issue_invited');
       });

       Schema::table('issues', function ($table) {
        $table->dropColumn('is_archived');
      });
    
    }
}
