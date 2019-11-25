<?php

namespace App\Http\Models;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Class WeatherStackForecast
 * @package App\Http\Models
 */
class WeatherStackForecast extends BaseForecast
{
    /**
     * WeatherStackForecast constructor.
     * @param $country
     * @param $city
     */
    public function __construct($country, $city)
    {
        $this->apiKey               = env('KEY_FOR_WEATHER_STACK');
        $this->currentWeatherUrl    = "http://api.weatherstack.com/current?access_key={$this->apiKey}&units=m&query={$city}";
    }

    /**
     * @param Client $client
     */
    public function getResponseBody(Client $client) : void
    {
        $response       = $client->get($this->currentWeatherUrl);
        $this->response = json_decode($response->getBody(), true);

        if (isset($this->response['error'])) {
            Log::error($this->response['error']);
        }
    }

    /**
     * @return float
     */
    public function getCurrentTemperature(): float
    {
        return $this->response['current']['temperature'] ?? null;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return isset($this->response['error']);
    }
}
