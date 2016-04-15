<?php

use Illuminate\Database\Seeder;

class RawLagouDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->dataCleansing();
        $cities = DB::table('lagou_job')
            ->select('city as name')
            ->groupBy('city')
            ->get();
        $CFS = DB::table('lagou_company')
            ->select('finance_stage as name')
            ->groupBy('finance_stage')
            ->get();
        $CFSP = DB::table('lagou_company')
            ->select('finance_stage_process as name')
            ->groupBy('finance_stage_process')
            ->get();
        $populations = DB::table('lagou_company')
            ->select('population as name')
            ->groupBy('population')
            ->get();
        $contractTypes = DB::table('lagou_job')
            ->select('contract_type as name')
            ->groupBy('contract_type')
            ->get();
        $eduDmds = DB::table('lagou_job')
            ->select('education_demand as name')
            ->groupBy('education_demand')
            ->get();
        $expDmds = DB::table('lagou_job')
            ->select('experience_demand as name')
            ->groupBy('experience_demand')
            ->get();
        $firstTypes = DB::table('lagou_job')
            ->select('first_type as name')
            ->groupBy('first_type')
            ->get();
        $types = DB::table('lagou_job')
            ->select('type as name')
            ->groupBy('type')
            ->get();
        \App\Models\City::createAll($this->processRawValues($cities));
        \App\Models\CompanyFinanceStage::createAll($this->processRawValues($CFS));
        \App\Models\CompanyFinanceStageProcess::createAll($this->processRawValues($CFSP));
        \App\Models\CompanyPopulation::createAll($this->processRawValues($populations));
        \App\Models\ContractType::createAll($this->processRawValues($contractTypes));
        \App\Models\JobExperienceDemand::createAll($this->processRawValues($expDmds));
        \App\Models\JobEducationDemand::createAll($this->processRawValues($eduDmds));
        \App\Models\JobFirstType::createAll($this->processRawValues($firstTypes));
        \App\Models\JobType::createAll($this->processRawValues($types));
        $industries = DB::table('lagou_company')
            ->select('industries as name')
            ->groupBy('industries')
            ->get();
        $industries = $this->processRawValues($industries);
        foreach ($industries as $industry) {
            $items = explode(',', $industry['name']);
            foreach ($items as $item) {
                \App\Models\CompanyIndustry::firstOrCreate([
                    'name' => trim($item)
                ]);
            }
        }

        $this->processRawJobs();
        $this->processRawCompanies();

    }

    public function processRawValues($values)
    {
        $values = array_filter($values, function ($value) {
            if (!empty($value->name) && $value->name != 'null') {
                return true;
            }

            return false;
        });

        return array_map(function ($value) {
            return ['name' => $value->name];
        }, $values);
    }

    public function dataCleansing()
    {
        DB::table('lagou_job')
            ->where('experience_demand', '1-3')
            ->update(['experience_demand' => '1-3年']);
        DB::table('lagou_job')
            ->where('experience_demand', '3-5')
            ->update(['experience_demand' => '3-5年']);
        DB::table('lagou_job')
            ->where('experience_demand', '5-10')
            ->update(['experience_demand' => '5-10年']);
        DB::table('lagou_job')
            ->where('experience_demand', '不限年')
            ->update(['experience_demand' => '不限']);
        DB::table('lagou_job')
            ->where('experience_demand', '无经验年')
            ->update(['experience_demand' => '无经验']);
        DB::table('lagou_job')
            ->where('experience_demand', '1年以下年')
            ->update(['experience_demand' => '1年以下']);
        DB::table('lagou_company')
            ->where('finance_stage_process', 'B 轮')
            ->update(['finance_stage_process' => 'B轮']);
    }

    public function processRawJobs()
    {
        $closure = function ($jobs) {
            foreach ($jobs as $job) {
                $attribute['id'] = $job->id;
                $attribute['name'] = $job->name;
                $attribute['salary_min'] = $job->salary_min;
                $attribute['salary_max'] = $job->salary_max;
                $attribute['company_id'] = $job->company_id;
                $attribute['advantage'] = $job->advantage;
                $attribute['create_time'] = $job->create_time;
                $attribute['address'] = $job->address;
                $attribute['detail'] = $job->detail;

                $type = \App\Models\JobType::where('name', $job->type)->first();
                if ($type) {
                    $attribute['type_id'] = $type->id;
                } else {
                    $attribute['type_id'] = null;
                }

                $firstType = \App\Models\JobFirstType::where('name', $job->first_type)->first();
                if ($firstType) {
                    $attribute['first_type_id'] = $firstType->id;
                } else {
                    $attribute['first_type_id'] = null;
                }

                $expDmd = \App\Models\JobExperienceDemand::where('name', $job->experience_demand)->first();
                if ($expDmd) {
                    $attribute['experience_demand_id'] = $expDmd->id;
                } else {
                    $attribute['experience_demand_id'] = null;
                }

                $eduDmd = \App\Models\JobEducationDemand::where('name', $job->education_demand)->first();
                if ($eduDmd) {
                    $attribute['education_demand_id'] = $eduDmd->id;
                } else {
                    $attribute['education_demand_id'] = null;
                }

                $city = \App\Models\City::where('name', $job->city)->first();
                if ($city) {
                    $attribute['city_id'] = $city->id;
                } else {
                    $attribute['city_id'] = null;
                }

                $contractType = \App\Models\ContractType::where('name', $job->contract_type)->first();
                if ($contractType) {
                    $attribute['contract_type_id'] = $contractType->id;
                } else {
                    $attribute['contract_type_id'] = null;
                }
                \App\Models\Job::create($attribute);
            }
        };
        DB::table('lagou_job')->chunk(100, $closure);

    }

    public function processRawCompanies()
    {
        $closure = function ($companies) {
            foreach ($companies as $company) {
                $attribute['id'] = $company->id;
                $attribute['name'] = $company->name;
                $attribute['short_name'] = $company->short_name;
                $attribute['logo'] = $company->logo;
                $attribute['job_process_rate_timely'] = $company->job_process_rate_timely;
                $attribute['days_cost_to_process'] = $company->days_cost_to_process;
                $attribute['labels'] = $company->labels;

                $city = \App\Models\City::where('name', $company->city)->first();
                if ($city) {
                    $attribute['city_id'] = $city->id;
                } else {
                    $attribute['city_id'] = null;
                }

                $population = \App\Models\CompanyPopulation::where('name', $company->population)->first();
                if ($population) {
                    $attribute['population_id'] = $population->id;
                } else {
                    $attribute['population_id'] = null;
                }

                $financeStage = \App\Models\CompanyFinanceStage::where('name', $company->finance_stage)->first();
                if ($financeStage) {
                    $attribute['finance_stage_id'] = $financeStage->id;
                } else {
                    $attribute['finance_stage_id'] = null;
                }

                $financeStageProcess = \App\Models\CompanyFinanceStageProcess::where('name', $company->finance_stage_process)->first();
                if ($financeStageProcess) {
                    $attribute['finance_stage_process_id'] = $financeStageProcess->id;
                } else {
                    $attribute['finance_stage_process_id'] = null;
                }

                \App\Models\Company::create($attribute);

                $industryArray = explode(',', $company->industries);
                array_walk($industryArray, function (&$value) {
                    $value = trim($value);
                });
                $industries = \App\Models\CompanyIndustry::select('id as industry_id')
                    ->whereIn(
                        'name',
                        $industryArray
                    )->get();
                $industries = $industries->toArray();
                array_walk($industries, function (&$value) use ($company) {
                    $value['company_id'] = $company->id;
                });
                DB::table('company_industry_relations')->insert($industries);
                echo "saved company " . $company->id . "\n";
            }
        };
        DB::table('lagou_company')->chunk(100, $closure);

    }
}
