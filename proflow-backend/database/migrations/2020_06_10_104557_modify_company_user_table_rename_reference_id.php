<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCompanyUserTableRenameReferenceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_user', function ($table) {
            $table->renameColumn('reference_id', 'role_id');  
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_user', function ($table) {
            $table->string('reference_id')->nullable();
         });
    }
}
