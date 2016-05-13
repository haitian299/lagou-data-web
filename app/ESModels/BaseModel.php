<?php namespace App\ESModels;

use Elasticsearch\ClientBuilder;

abstract class BaseModel
{
    protected $params = [];
    protected $index  = 'lagou';
    protected $type;
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
        $this->params = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => []
        ];
    }

    public function setParamsBody($key, $value)
    {
        $array = &$this->params['body'];
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $this;
    }

    public function getResult($key = null)
    {
        $response = $this->client->search($this->params);
        if (!is_null($key)) {
            foreach (explode('.', $key) as $segment) {
                $response = $response[$segment];
            }
        }

        return $response;
    }
}
