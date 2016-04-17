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
        $typeIds = array_flatten(\App\Models\JobType::select('id')->get()->toArray());
        $typeValues = array_flatten(\App\Models\JobType::select('name')->get()->toArray());
        $types = array_combine($typeIds, $typeValues);

        $firstTypeIds = array_flatten(\App\Models\JobFirstType::select('id')->get()->toArray());
        $firstTypeValues = array_flatten(\App\Models\JobFirstType::select('name')->get()->toArray());
        $firstTypes = array_combine($firstTypeIds, $firstTypeValues);

        $expDmdIds = array_flatten(\App\Models\JobExperienceDemand::select('id')->get()->toArray());
        $expDmdValues = array_flatten(\App\Models\JobExperienceDemand::select('name')->get()->toArray());
        $expDmds = array_combine($expDmdIds, $expDmdValues);

        $eduDmdIds = array_flatten(\App\Models\JobEducationDemand::select('id')->get()->toArray());
        $eduDmdValues = array_flatten(\App\Models\JobEducationDemand::select('name')->get()->toArray());
        $eduDmds = array_combine($eduDmdIds, $eduDmdValues);

        $cityIds = array_flatten(\App\Models\City::select('id')->get()->toArray());
        $cityValues = array_flatten(\App\Models\City::select('name')->get()->toArray());
        $cities = array_combine($cityIds, $cityValues);

        $contractTypeIds = array_flatten(\App\Models\ContractType::select('id')->get()->toArray());
        $contractTypeValues = array_flatten(\App\Models\ContractType::select('name')->get()->toArray());
        $contractTypes = array_combine($contractTypeIds, $contractTypeValues);
        $closure = function ($jobs) use (
            $types,
            $firstTypes,
            $expDmds,
            $eduDmds,
            $cities,
            $contractTypes
        ) {
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

                if ($type = array_search($job->type, $types)) {
                    $attribute['type_id'] = $type;
                } else {
                    $attribute['type_id'] = null;
                }

                if ($firstType = array_search($job->first_type, $firstTypes)) {
                    $attribute['first_type_id'] = $firstType;
                } else {
                    $attribute['first_type_id'] = null;
                }

                if ($expDmd = array_search($job->experience_demand, $expDmds)) {
                    $attribute['experience_demand_id'] = $expDmd;
                } else {
                    $attribute['experience_demand_id'] = null;
                }

                if ($eduDmd = array_search($job->education_demand, $eduDmds)) {
                    $attribute['education_demand_id'] = $eduDmd;
                } else {
                    $attribute['education_demand_id'] = null;
                }

                if ($city = array_search($job->city, $cities)) {
                    $attribute['city_id'] = $city;
                } else {
                    $attribute['city_id'] = null;
                }

                if ($contractType = array_search($job->contract_type, $contractTypes)) {
                    $attribute['contract_type_id'] = $contractType;
                } else {
                    $attribute['contract_type_id'] = null;
                }
                \App\Models\Job::create($attribute);
                echo "saved job " . $job->id . "\n";
            }
        };
        DB::table('lagou_job')->chunk(100, $closure);

    }

    public function processRawCompanies()
    {
        $cityIds = array_flatten(\App\Models\City::select('id')->get()->toArray());
        $cityValues = array_flatten(\App\Models\City::select('name')->get()->toArray());
        $cities = array_combine($cityIds, $cityValues);

        $populationIds = array_flatten(\App\Models\CompanyPopulation::select('id')->get()->toArray());
        $populationValues = array_flatten(\App\Models\CompanyPopulation::select('name')->get()->toArray());
        $populations = array_combine($populationIds, $populationValues);

        $financeStageIds = array_flatten(\App\Models\CompanyFinanceStage::select('id')->get()->toArray());
        $financeStageValues = array_flatten(\App\Models\CompanyFinanceStage::select('name')->get()->toArray());
        $financeStages = array_combine($financeStageIds, $financeStageValues);

        $financeStageProcessIds = array_flatten(\App\Models\CompanyFinanceStageProcess::select('id')->get()->toArray());
        $financeStageProcessValues = array_flatten(\App\Models\CompanyFinanceStageProcess::select('name')->get()->toArray());
        $financeStageProcesses = array_combine($financeStageProcessIds, $financeStageProcessValues);

        $closure = function ($companies) use (
            $cities,
            $populations,
            $financeStages,
            $financeStageProcesses
        ) {
            foreach ($companies as $company) {
                $attribute['id'] = $company->id;
                $attribute['name'] = $company->name;
                $attribute['short_name'] = $company->short_name;
                $attribute['logo'] = $company->logo;
                $attribute['job_process_rate_timely'] = $company->job_process_rate_timely;
                $attribute['days_cost_to_process'] = $company->days_cost_to_process;
                $attribute['labels'] = $company->labels;

                if ($city = array_search($company->city, $cities)) {
                    $attribute['city_id'] = $city;
                } else {
                    $attribute['city_id'] = null;
                }

                if ($population = array_search($company->population, $populations)) {
                    $attribute['population_id'] = $population;
                } else {
                    $attribute['population_id'] = null;
                }

                if ($financeStage = array_search($company->finance_stage, $financeStages)) {
                    $attribute['finance_stage_id'] = $financeStage;
                } else {
                    $attribute['finance_stage_id'] = null;
                }

                if ($financeStageProcess = array_search($company->finance_stage_process, $financeStageProcesses)) {
                    $attribute['finance_stage_process_id'] = $financeStageProcess;
                } else {
                    $attribute['finance_stage_process_id'] = null;
                }

                $savedCompany = \App\Models\Company::create($attribute);

                $industryArray = explode(',', $company->industries);
                array_walk($industryArray, function (&$value) {
                    $value = trim($value);
                });
                $industries = \App\Models\CompanyIndustry::select('id')
                    ->whereIn(
                        'name',
                        $industryArray
                    )->get();
                $industries = array_flatten($industries->toArray());
                $savedCompany->industries()->sync($industries);
                echo "saved company " . $company->id . "\n";
            }
        };
        DB::table('lagou_company')->chunk(100, $closure);
    }
}
