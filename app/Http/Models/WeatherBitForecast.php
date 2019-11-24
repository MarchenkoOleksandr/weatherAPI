<?php

namespace App\Http\Models;

use GuzzleHttp\Client;

/**
 * Class WeatherBitForecast
 * @package App\Http\Models
 */
class WeatherBitForecast extends BaseForecast
{
//$response3 = $client->get('http://api.weatherbit.io/v2.0/current?key=fcc38a8cf4424de49640ea6375c90917&country=' . $request->get('country') . '&city=' . $request->get('city'));

    /**
     * WeatherBitForecast constructor.
     * @param $country
     * @param $city
     */
    public function __construct($country, $city)
    {
        $this->apiKey               = 'fcc38a8cf4424de49640ea6375c90917';
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
