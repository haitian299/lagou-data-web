<?php

use Illuminate\Database\Seeder;

class ElasticSearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $es;

    protected $chunkSize = 500;

    public function __construct()
    {
        $this->es = \Elasticsearch\ClientBuilder::create()->build();
    }

    public function run()
    {
        $this->createIndex();
        $this->indexJobs();
        $this->indexCompanies();
    }

    public function createIndex()
    {
        $indexParams = [
            'index' => 'lagou',
            'body'  => [
                'mappings' => [
                    'job'     => [
                        '_source'    => [
                            'enabled' => true
                        ],
                        'properties' => [
                            "address"           => [
                                "type"     => "string",
                                "analyzer" => "ik_smart"
                            ],
                            "advantage"         => [
                                "type"     => "string",
                                "analyzer" => "ik_smart"
                            ],
                            "city"              => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "company_id"        => [
                                "type" => "long"
                            ],
                            "contract_type"     => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "create_time"       => [
                                "type"   => "date",
                                "format" => "yyy-MM-dd HH:mm:ss"
                            ],
                            "created_at"        => [
                                "type"   => "date",
                                "format" => "yyy-MM-dd HH:mm:ss"
                            ],
                            "detail"            => [
                                "type"     => "string",
                                "analyzer" => "ik_smart"
                            ],
                            "education_demand"  => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "experience_demand" => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "first_type"        => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "id"                => [
                                "type" => "long"
                            ],
                            "name"              => [
                                "type"     => "string",
                                "analyzer" => "ik_smart"
                            ],
                            "salary_max"        => [
                                "type" => "integer"
                            ],
                            "salary_min"        => [
                                "type" => "integer"
                            ],
                            "type"              => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "updated_at"        => [
                                "type"   => "date",
                                "format" => "yyy-MM-dd HH:mm:ss"
                            ]
                        ]
                    ],
                    'company' => [
                        '_source'    => [
                            'enabled' => true
                        ],
                        'properties' => [
                            "city"                    => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "created_at"              => [
                                "type"   => "date",
                                "format" => "yyy-MM-dd HH:mm:ss"
                            ],
                            "id"                      => [
                                "type" => "long"
                            ],
                            "name"                    => [
                                "type"     => "string",
                                "analyzer" => "ik_smart"
                            ],
                            "short_name"              => [
                                "type"     => "string",
                                "analyzer" => "ik_smart"
                            ],
                            "logo"                    => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "population"              => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "job_process_rate_timely" => [
                                "type" => "integer"
                            ],
                            "days_cost_to_process"    => [
                                "type" => "integer"
                            ],
                            "finance_stage"           => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "finance_stage_process"   => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "labels"                  => [
                                "type"     => "string",
                                "analyzer" => "ik_smart"
                            ],
                            "industries"              => [
                                "type"  => "string",
                                "index" => "not_analyzed"
                            ],
                            "updated_at"              => [
                                "type"   => "date",
                                "format" => "yyy-MM-dd HH:mm:ss"
                            ]
                        ]
                    ]
                ]

            ]
        ];
        $this->es->indices()->create($indexParams);
    }

    public function indexJobs()
    {
        $closure = function ($jobs) {
            $params = [];
            foreach ($jobs as $job) {
                $params['body'][] = [
                    'index' => [
                        '_index' => 'lagou',
                        '_type'  => 'job',
                        '_id'    => $job->id
                    ]
                ];
                $params['body'][] = $job;
            }
            $this->es->bulk($params);
            echo "saved {$this->chunkSize} jobs\n";
        };

        DB::table('lagou_job')->chunk($this->chunkSize, $closure);
    }

    public function indexCompanies()
    {
        $closure = function ($companies) {
            $params = [];
            foreach ($companies as $company) {
                $params['body'][] = [
                    'index' => [
                        '_index' => 'lagou',
                        '_type'  => 'company',
                        '_id'    => $company->id
                    ]
                ];
                $company->labels = explode(',', $company->labels);
                $company->industries = explode(',', $company->industries);
                $params['body'][] = $company;
            }
            $this->es->bulk($params);
            echo "saved {$this->chunkSize} companies\n";
        };

        DB::table('lagou_company')->chunk($this->chunkSize, $closure);
    }
}
