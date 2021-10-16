<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('additional_info')->nullable();
            $table->date('due_date')->nullable();
            $table->string('people_involved_id')->nullable();
            $table->enum('visibility',['draft','private','company','archived'])->nullable();
            $table->string('people_invited_id')->nullable();
            $table->string('people_invite_email')->nullable();
            $table->tinyInteger('is_resolved')->default(0);
            $table->string('resolve_text')->nullable();
            $table->tinyInteger('priority')->default(0)->comment('0=urgent,1=high,2=medium,3=low');
            $table->bigInteger('company_id')->nullable();
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
        Schema::dropIfExists('issues');
    }
}
