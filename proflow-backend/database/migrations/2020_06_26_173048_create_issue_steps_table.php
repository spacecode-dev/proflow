<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssueStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_steps', function (Blueprint $table) {
            $table->id();
            $table->text('text')->nullable();
            $table->date('due_date')->nullable();
            $table->tinyInteger('is_resolved')->default(0);
            $table->string('assigned_to')->nullable();
            $table->bigInteger('issue_id')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issue_steps');
    }
}
