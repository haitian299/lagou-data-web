<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JobAndCompanyAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('company_finance_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('company_finance_stage_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('company_industries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('company_industry_relations', function (Blueprint $table) {
            $table->integer('company_id')->unsigned();
            $table->integer('industry_id')->unsigned();
            $table->primary(['company_id', 'industry_id']);
        });
        Schema::create('company_populations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('contract_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('job_education_demands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('job_experience_demands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('job_first_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        Schema::create('job_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('job_first_type_id')->unsigned();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
