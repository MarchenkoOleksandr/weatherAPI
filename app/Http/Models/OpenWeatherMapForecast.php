<?php

namespace App\Http\Models;

use GuzzleHttp\Client;

/**
 * Class OpenWeatherMapForecast
 * @package App\Http\Models
 */
class OpenWeatherMapForecast extends BaseForecast
{
//    $response2 = $client->get('http://api.openweathermap.org/data/2.5/weather?units=metric&APPID=3108af8e2bbc6b946ddf01a1908b8a83&q=' . $request->get('city'));

    /**
     * OpenWeatherMapForecast constructor.
     * @param $country
     * @param $city
     */
    public function __construct($country, $city)
    {
        $this->apiKey               = '3108af8e2bbc6b946ddf01a1908b8a83';
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
