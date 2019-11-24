<?php

namespace App\Http\Models;

use GuzzleHttp\Client;

/**
 * Class OpenWeatherMapForecast
 * @package App\Http\Models
 */
class OpenWeatherMapForecast extends BaseForecast
{
    /**
     * OpenWeatherMapForecast constructor.
     * @param $country
     * @param $city
     */
    public function __construct($country, $city)
    {
        $this->apiKey               = env('KEY_FOR_OPEN_WEATHER_MAP');
        $this->currentWeatherUrl    = "http://api.openweathermap.org/data/2.5/weather?units=metric&APPID={$this->apiKey}&q={$city},{$country}";
    }

    public function getResponseBody(Client $client): void
    {
        try {
            $response       = $client->get($this->currentWeatherUrl);
            $this->response = json_decode($response->getBody(), true);
        } catch (\Exception $ex) {
            $this->response = null;
        }
    }

    /**
     * @return float
     */
    public function getCurrentTemperature(): float
    {
        return $this->response['main']['temp'] ?? null;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !isset($this->response);
    }
}
