<?php

namespace App\Http\Models\UserRequests;

use App\Http\Models\Forecasts\OpenWeatherMapForecast;
use App\Http\Models\Forecasts\WeatherBitForecast;
use App\Http\Models\Forecasts\WeatherStackForecast;

/**
 * Class BaseRequest
 *
 * @package App\Http\Models\UserRequests
 */
class BaseRequest
{
    protected $weatherClasses = [];

    /**
     * BaseRequest constructor.
     */
    public function __construct()
    {
        $this->weatherClasses = [
            WeatherStackForecast::class,
            OpenWeatherMapForecast::class,
            WeatherBitForecast::class,
        ];
    }
}
