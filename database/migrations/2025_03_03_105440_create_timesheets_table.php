<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('task_name', 175);
            $table->date('date')->nullable();
            $table->integer('minutes')->nullable()->comment('storing in minutes and apply hour conversion');
            $table->foreignId('user_id');
            $table->foreignId('project_id');
            $table->timestamps();
            $table->unique(['user_id', 'project_id'], 'user_project');
            $table->index('project_id');
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_sheets');
    }
}
