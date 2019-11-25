<?php

namespace App\Http\Models\Forecasts;

use GuzzleHttp\Client;

/**
 * Class BaseForecast
 * @package App\Http\Models
 */
abstract class BaseForecast
{
    protected $currentWeatherUrl;
    protected $apiKey;
    protected $response;

    abstract public function getResponseBody(Client $client) : void;

    abstract public function getCurrentTemperature() : float;

    abstract public function hasErrors() : bool;
}
