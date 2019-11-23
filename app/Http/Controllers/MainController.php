<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->method() == "POST" && $request->get('country') && $request->get('city')) {
            $client = new Client();
            $response1 = $client->get('http://api.weatherstack.com/current?access_key=9d0c560c445bb87487e69e2ff625bef4&query=' . $request->get('city'));
            $response2 = $client->get('http://api.openweathermap.org/data/2.5/weather?units=metric&APPID=3108af8e2bbc6b946ddf01a1908b8a83&q=' . $request->get('city'));
            $response3 = $client->get('http://api.weatherbit.io/v2.0/current?key=fcc38a8cf4424de49640ea6375c90917&country=' . $request->get('country') . '&city=' . $request->get('city'));

            $result1 = json_decode($response1->getBody(), true);
            $result2 = json_decode($response2->getBody(), true);
            $result3 = json_decode($response3->getBody(), true);


            return view('index', [
                'method' => $request->method(),
                'data1' => $result1['location']['name'] . ' - ' . $result1['current']['temperature'],
                'data2' => $result2['name'] . ' - ' . $result2['main']['temp'],
                'data3' => $result3['data'][0]['city_name'] . ' - ' . $result3['data'][0]['temp'],
                'avg' => 'AVG: ' . (($result1['current']['temperature'] + $result2['main']['temp'] + $result3['data'][0]['temp']) / 3)
            ]);
        }

        return view('index', [
            'method' => $request->method()
        ]);
    }
}
