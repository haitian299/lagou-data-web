<?php namespace App\ESModels;

class Tech extends BaseModel
{
    protected $type     = 'job';
    protected $keywords = ['java', 'ios', 'php', 'python', 'c++', 'android', '前端', 'node.js', 'go', 'html5', 'hadoop'];

    public function analyzeSalary()
    {
        $filters = [];
        foreach ($this->keywords as $keyword) {
            $filters[$keyword] = [
                'term' => ['name' => $keyword]
            ];
        }
        $this->setParamsBody('size', 0)
            ->setParamsBody('aggs.tech.filters', [
                'filters' => $filters
            ])->setParamsBody('aggs.tech.aggs', [
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
        $filters = [];
        foreach ($this->keywords as $keyword) {
            $filters[$keyword] = [
                'term' => ['name' => $keyword]
            ];
        }
        $this->setParamsBody('size', 0)
            ->setParamsBody('aggs.tech.filters', [
                'filters' => $filters
            ]);

        return $this;
    }
}

