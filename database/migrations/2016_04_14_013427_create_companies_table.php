<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('short_name');
            $table->string('logo');
            $table->integer('city_id')->unsigned()->nullable();
            $table->integer('population_id')->unsigned()->nullable();
            $table->tinyInteger('job_process_rate_timely')->unsigned()->nullable();
            $table->tinyInteger('days_cost_to_process')->unsigned()->nullable();
            $table->integer('finance_stage_id')->unsigned()->nullable();
            $table->integer('finance_stage_process_id')->unsigned()->nullable();
            $table->string('labels');
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
        Schema::drop('companies');
    }
}
