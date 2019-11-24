<?php

namespace App\Http\Models;

use GuzzleHttp\Client;

/**
 * Class WeatherBitForecast
 * @package App\Http\Models
 */
class WeatherBitForecast extends BaseForecast
{
    /**
     * WeatherBitForecast constructor.
     * @param $country
     * @param $city
     */
    public function __construct($country, $city)
    {
        $this->apiKey               = env('KEY_FOR_WEATHER_BIT');
        $this->currentWeatherUrl    = "http://api.weatherbit.io/v2.0/current?key={$this->apiKey}&country={$country}&city={$city}";
    }

    /**
     * @param Client $client
     */
    public function getResponseBody(Client $client): void
    {
        $response       = $client->get($this->currentWeatherUrl);
        $this->response = json_decode($response->getBody(), true);
    }

    public function getCurrentTemperature(): float
    {
        return $this->response['data'][0]['temp'] ?? null;
    }

    public function hasErrors(): bool
    {
        return !isset($this->response);
    }
}
