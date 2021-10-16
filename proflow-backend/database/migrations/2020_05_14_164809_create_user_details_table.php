<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('department_id')->nullable();
            $table->tinyInteger('managing_people')->default(0);
            $table->text('invitee')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('google_token')->nullable();
            $table->tinyInteger('signup_step')->default(0);
            $table->integer('invited_by')->nullable()->default(null);
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
        Schema::dropIfExists('user_detail');
    }
}
