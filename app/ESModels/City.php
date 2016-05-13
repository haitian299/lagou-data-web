<?php namespace App\ESModels;

class City extends BaseModel
{
    protected $type = 'job';

    public function analyzeSalary($size = 20)
    {
        $this->setParamsBody('size', 0)
            ->setParamsBody('aggs.cities.terms', [
                'field' => 'city',
                'size'  => $size
            ])
            ->setParamsBody('aggs.cities.aggs', [
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

    public function analyzeCount($size = 20)
    {
        $this->setParamsBody('size', 0)
            ->setParamsBody('aggs.cities.terms', [
                'field' => 'city',
                'size'  => $size
            ]);

        return $this;
    }
}

