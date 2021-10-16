<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IssueUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_user', function (Blueprint $table) {
            $table->id();
            $table->integer('issue_id');
            $table->integer('user_id');
            $table->tinyInteger('is_follower')->default(0);
            $table->tinyInteger('is_mention')->default(0);
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
        Schema::dropIfExists('issue_user');
    }
}
