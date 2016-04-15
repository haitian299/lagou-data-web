<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('name');
            $table->integer('type_id')->unsigned()->nullable();
            $table->tinyInteger('salary_min');
            $table->tinyInteger('salary_max');
            $table->integer('first_type_id')->unsigned()->nullable();
            $table->integer('experience_demand_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned()->nullable();
            $table->integer('education_demand_id')->unsigned()->nullable();
            $table->integer('company_id');
            $table->integer('contract_type_id')->unsigned()->nullable();
            $table->string('advantage');
            $table->timestamp('create_time')->nullable();
            $table->string('address');
            $table->longText('detail');
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
        Schema::drop('jobs');
    }
}
