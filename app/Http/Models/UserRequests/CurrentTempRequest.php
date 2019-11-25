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

    public function makeApiRequest()
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
     * @param      $startTime
     *
     * @return array
     */
    public function getDataForTemplate(bool $isPostMethod, $startTime): array
    {
        $data = [];

        $data['countriesList'] = json_decode($this->countriesList, true);
        $data['city']          = $this->city;
        $data['emptyResponse'] = $this->isEmptyRequest();
        $data['isPostMethod']  = $isPostMethod;

        if ($data['emptyResponse'] || !$data['isPostMethod']) {
            return $data;
        }

        $data['result'] = [
            'avg'           => $this->getAvgTemperature(),
            'total'         => count($this->weatherClasses),
            'errorsCounter' => $this->errorsCounter,
        ];

        $data['time'] = round((microtime(true) - $startTime) * 1000) . ' milliseconds';

        return $data;
    }
}
