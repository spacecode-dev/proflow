<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyIssueUserTableAddIsInvitedId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issue_user', function ($table) {
            $table->tinyInteger('is_invited')->default(0)->after('is_mention');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issue_user', function ($table) {
            $table->dropColumn('is_invited')->default(0);
       });
    }
}
