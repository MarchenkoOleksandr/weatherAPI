<?php

namespace App\Http\Controllers;

use App\Http\Models\OpenWeatherMapForecast;
use App\Http\Models\WeatherBitForecast;
use App\Http\Models\WeatherStackForecast;
use GuzzleHttp\Client;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class MainController
 *
 * @package App\Http\Controllers
 */
class MainController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $countriesList = file_get_contents(env('DOCUMENT_ROOT') . '/database/countries.json');

        if ($request->isMethod('POST')) {
            if ($this->isEmptyRequest($request->get('country'), $request->get('city'))) {
                return view('index', [
                    'isPostMethod'  => $request->isMethod('POST'),
                    'emptyResponse' => true
                ]);
            }

            $client = new Client();

            $errorsCounter  = 0;
            $temperatures   = array();
            $weatherClasses = array(
                WeatherStackForecast::class,
                OpenWeatherMapForecast::class,
                WeatherBitForecast::class
            );

            $time_start = microtime(true);
            foreach ($weatherClasses as $class) {
                $c = new $class($request->get('country'), $request->get('city'));
                $c->getResponseBody($client);
                if ($t = $c->hasErrors()) {
                    $errorsCounter++;
                } else {
                    $temperatures[] = $c->getCurrentTemperature();
                }
            }

            return view('index', [
                'isPostMethod'  => $request->isMethod('POST'),
                'countriesList' => json_decode($countriesList, true),
                'city'          => $request->get('city'),
                'avg'           => $this->getAvgTemperature($temperatures),
                'total'         => count($weatherClasses),
                'errorsCounter' => $errorsCounter,
                'time'          => round((microtime(true) - $time_start) * 1000) . ' milliseconds'
            ]);
        }

        return view('index', [
            'isPostMethod' => $request->isMethod('POST'),
            'countriesList' => json_decode($countriesList, true)
        ]);
    }

    /**
     * @param array $temperatures
     * @return string
     */
    private function getAvgTemperature(array $temperatures): string
    {
        $result = 0;

        if (empty($temperatures)) {
            return 'failed (check the entered data or try later)';
        }

        foreach ($temperatures as $temperature) {
            $result += $temperature;
        }

        return round($result / count($temperatures), 2) . ' C';
    }

    /**
     * @param $city
     * @param $country
     * @return bool
     */
    private function isEmptyRequest($city, $country): bool
    {
        if (!$city || !$country) {
            return true;
        }

        return false;
    }
}
