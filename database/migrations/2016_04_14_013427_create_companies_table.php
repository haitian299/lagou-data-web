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
            $table->integer('id')->unsigned();
            $table->string('name');
            $table->string('short_name');
            $table->string('logo');
            $table->integer('city')->unsigned();
            $table->integer('population')->unsigned();
            $table->tinyInteger('job_process_rate_timely')->unsigned()->nullable();
            $table->tinyInteger('days_cost_to_process')->unsigned()->nullable();
            $table->integer('finance_stage')->unsigned();
            $table->integer('finance_stage_process')->nullable();
            $table->timestamps();
            $table->primary('id');
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
