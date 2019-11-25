<?php

namespace App\Http\Models\UserRequests;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Class CurrentTempRequest
 *
 * @package App\Http\Models\UserRequests
 */
class CurrentTempRequest extends BaseRequest
{
    protected $city;
    protected $country;
    protected $countriesList;
    protected $errorsCounter = 0;
    protected $temperatures  = [];

    /**
     * CurrentTempRequest constructor.
     *
     * @param $city
     * @param $country
     */
    public function __construct($country = '', $city = '')
    {
        parent::__construct();

        $this->city          = $city;
        $this->country       = $country;
        $this->countriesList = file_get_contents(env('DOCUMENT_ROOT') . '/database/countries.json');
    }

    public function makeRequest() : void
    {
        $startTime    = microtime(true);
        $redisClient  = new \Predis\Client();
        $this->result = $this->makeRedisRequest($redisClient);

        if (empty($this->result)) {
            $this->result = $this->makeApiRequest();
            $this->saveToRedis($redisClient);
        }

        $this->requestTime = round((microtime(true) - $startTime) * 1000);
    }

    /**
     * @return array
     */
    public function makeApiRequest() : array
    {
        $client = new Client();

        foreach ($this->weatherClasses as $class) {
            $c = new $class($this->country, $this->city);
            $c->getResponseBody($client);
            if (!$c->hasErrors() && is_numeric($c->getCurrentTemperature())) {
                $this->temperatures[] = $c->getCurrentTemperature();
            } else {
                $this->errorsCounter++;
                Log::warning("Failed to retrieve temperature for {$this->city} ({$this->country})");
            }
        }

        return array(
            'avg'           => $this->getAvgTemperature(),
            'total'         => count($this->weatherClasses),
            'errorsCounter' => $this->errorsCounter
        );
    }

    /**
     * @param \Predis\Client $redisClient
     *
     * @return array
     */
    public function makeRedisRequest(\Predis\Client $redisClient) : array
    {
        if ($redisClient->isConnected() && $redisResult = $redisClient->get("{$this->country}:{$this->city}")) {
            return json_decode($redisResult, true);
        }

        return array();
    }

    /**
     * @return bool
     */
    public function isEmptyRequest(): bool
    {
        return !($this->city && $this->country);
    }

    /**
     * @return string
     */
    private function getAvgTemperature(): string
    {
        $result = 0;

        if (empty($this->temperatures)) {
            return 'failed (check the entered data or try later)';
        }

        foreach ($this->temperatures as $temperature) {
            $result += $temperature;
        }

        return round($result / count($this->temperatures), 2) . ' C';
    }

    /**
     * @param bool $isPostMethod
     *
     * @return array
     */
    public function prepareDataForTemplate(bool $isPostMethod): array
    {
        $data = [];

        $data['countriesList'] = json_decode($this->countriesList, true);
        $data['city']          = $this->city;
        $data['emptyResponse'] = $this->isEmptyRequest();
        $data['isPostMethod']  = $isPostMethod;

        if ($data['emptyResponse'] || !$data['isPostMethod']) {
            return $data;
        }

        $data['result'] = $this->result;
        $data['time']   = $this->requestTime . ' milliseconds';

        return $data;
    }

    private function saveToRedis(\Predis\Client $redisClient) : void
    {
        if ($redisClient->isConnected() && count($this->temperatures) > $this->errorsCounter) {
            $redisClient->set("{$this->country}:{$this->city}",
                json_encode($this->result), "EX", env('REDIS_TIME_EXPIRE'));
        }
    }
}
