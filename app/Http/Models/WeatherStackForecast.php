<?php

namespace App\Http\Models;

use GuzzleHttp\Client;

/**
 * Class WeatherStackForecast
 * @package App\Http\Models
 */
class WeatherStackForecast extends BaseForecast
{
    //http://api.weatherstack.com/current?access_key=9d0c560c445bb87487e69e2ff625bef4&query=' . $request->get('city')
    /**
     * WeatherStackForecast constructor.
     * @param $country
     * @param $city
     */
    public function __construct($country = '', $city = '')
    {
        $this->apiKey = '9d0c560c445bb87487e69e2ff625bef4';
        $this->currentWeatherUrl = "http://api.weatherstack.com/current?access_key={$this->apiKey}&units=m&query={$city}";
    }

    /**
     * @param Client $client
     */
    public function getResponseBody(Client $client) : void
    {
        $response = $client->get($this->currentWeatherUrl);
        $this->response = json_decode($response->getBody(), true);
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
