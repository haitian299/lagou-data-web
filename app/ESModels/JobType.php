<?php namespace App\ESModels;

class JobType extends BaseModel
{
    protected $type = 'job';

    public function analyzeSalary()
    {
        $this->setParamsBody('size', 0)
            ->setParamsBody('aggs.jobTypes.terms', [
                'field' => 'first_type'
            ])
            ->setParamsBody('aggs.jobTypes.aggs', [
                'salary_stat' => [
                    'percentiles' => [
                        'field'    => 'salary_avg',
                        'percents' => [25, 50, 75]
                    ]
                ],
                'salary_max'  => [
                    'max' => [
                        'field' => 'salary_avg'
                    ]
                ],
                'salary_min'  => [
                    'min' => [
                        'field' => 'salary_avg'
                    ]
                ]
            ]);

        return $this;
    }

    public function analyzeCount()
    {
        $this->setParamsBody('size', 0)
            ->setParamsBody('aggs.jobTypes.terms', [
                'field' => 'first_type'
            ]);

        return $this;
    }
}

