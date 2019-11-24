<?php

namespace App\Http\Controllers;

use App\Http\Models\BaseForecast;
use App\Http\Models\WeatherStackForecast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
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
        if ($request->isMethod('POST')) {
            if ($this->isEmptyRequest($request->get('country'), $request->get('city'))) {
                return view('index', [
                    'isPostMethod' => $request->isMethod('POST'),
                    'emptyResponse' => true
                ]);
            }


            $client = new Client();
//            $response1 = $client->get('http://api.weatherstack.com/current?access_key=9d0c560c445bb87487e69e2ff625bef4&query=' . $request->get('city'));
//            $response2 = $client->get('http://api.openweathermap.org/data/2.5/weather?units=metric&APPID=3108af8e2bbc6b946ddf01a1908b8a83&q=' . $request->get('city'));
//            $response3 = $client->get('http://api.weatherbit.io/v2.0/current?key=fcc38a8cf4424de49640ea6375c90917&country=' . $request->get('country') . '&city=' . $request->get('city'));

            $errorsCounter = 0;
            $temperatures = array();
            $weatherClasses  = array(
                WeatherStackForecast::class
            );

            $time_start = microtime(true);
            foreach ($weatherClasses as $class) {
                $c = new $class($request->get('country'), $request->get('city'));
                $c->getResponseBody($client);
                if ($c->hasErrors()) {
                    $errorsCounter++;
                } else {
                    $temperatures[] = $c->getCurrentTemperature();
                }
            }

//            $result1 = json_decode($response1->getBody(), true);
//            $result2 = json_decode($response2->getBody(), true);
//            $result3 = json_decode($response3->getBody(), true);


            return view('index', [
                'isPostMethod' => $request->isMethod('POST'),
                'city' => $request->get('city'),
                'avg' => $this->getAvgTemperature($temperatures),
                'total' => count($weatherClasses),
                'errorsCounter' => $errorsCounter,
                'time' => round((microtime(true) - $time_start) * 1000) . ' milliseconds'

//                'data1' => $result1['location']['name'] . ' - ' . $result1['current']['temperature'],
//                'data2' => $result2['name'] . ' - ' . $result2['main']['temp'],
//                'data3' => $result3['data'][0]['city_name'] . ' - ' . $result3['data'][0]['temp'],
            ]);
        }

        return view('index', [
            'isPostMethod' => $request->isMethod('POST')
        ]);
    }

    /**
     * @param array $temperatures
     * @return string
     */
    private function getAvgTemperature(array $temperatures) : string {
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
    private function isEmptyRequest($city, $country) : bool {
        if (!$city || !$country) {
            return true;
        }

        return false;
    }
}
