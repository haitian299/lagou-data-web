<?php

namespace App\Http\Controllers;

use App\ESModels\JobType;
use App\ESModels\Tech;
use App\Http\Requests;
use App\ESModels\City;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function chart()
    {
        return view('chart');
    }

    public function api(Request $request)
    {
        switch ($request->input('base')) {
            case 'tech':
                $tech = new Tech();
                switch ($request->input('analyze')) {
                    case 'count':
                        $result = [];
                        foreach ($tech->analyzeCount()->getResult('aggregations.tech.buckets') as $key => $value) {
                            $result[] = array_merge($value, ['key' => $key]);
                        }
                        usort($result, function ($a, $b) {
                            if ($a['doc_count'] === $b['doc_count']) {
                                return 0;
                            }

                            return ($a['doc_count'] > $b['doc_count']) ? -1 : 1;
                        });

                        return $result;
                        break;
                    case 'salary':
                    default:
                        $result = [];
                        foreach ($tech->analyzeSalary()->getResult('aggregations.tech.buckets') as $key => $value) {
                            $result[] = array_merge($value, ['key' => $key]);
                        }
                        usort($result, function ($a, $b) {
                            if ($a['doc_count'] === $b['doc_count']) {
                                return 0;
                            }

                            return ($a['doc_count'] > $b['doc_count']) ? -1 : 1;
                        });

                        return $result;
                        break;
                }
            case 'jobType':
                $jobType = new JobType();
                switch ($request->input('analyze')) {
                    case 'count':
                        return $jobType->analyzeCount()->getResult('aggregations.jobTypes.buckets');
                        break;
                    case 'salary':
                    default:
                        return $jobType->analyzeSalary()->getResult('aggregations.jobTypes.buckets');
                        break;
                }
            case 'city':
            default:
                $city = new City();
                switch ($request->input('analyze')) {
                    case 'count':
                        return $city->analyzeCount()->getResult('aggregations.cities.buckets');
                        break;
                    case 'salary':
                    default:
                        return $city->analyzeSalary()->getResult('aggregations.cities.buckets');
                }
        }
    }
}
