<?php

namespace App\Http\Controllers;

use App\Http\Models\UserRequests\CurrentTempRequest;
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
        $currentTemp = new CurrentTempRequest($request->get('country'), $request->get('city'));

        if ($request->isMethod('POST')) {
            $currentTemp->makeRequest();

            return view('index', $currentTemp->prepareDataForTemplate(true));
        }

        return view('index', $currentTemp->prepareDataForTemplate(false));
    }
}
