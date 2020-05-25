<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Cardinity;

class ShopController extends Controller
{

    /**
     * Redirect to shopping cart page
     */
    public function cart(Request $request)
    {
        $amount = $request->amount;

        return view("cart", compact('amount'));
    }

    
    /**
     * Redirect to credentials page
     */
    public function credentials(Request $request)
    {
        $amount = $request->amount;
        
        return view("credentials", compact('amount'));
    }


    /**
     * Process credentials and pass data to payment service
     */
    public function process(Request $request)
    {
        $date = explode(' ', $request->expdate);
        
        foreach ($date as $d) {
            if (strlen($d) == 4) $year = $d;
            elseif (strlen($d) == 2) $month = $d;
        }

        $params['amount'] = $request->amount;
        $params['name'] = $request->name;
        $params['pan'] = $request->pan;
        $params['year'] = $year;
        $params['month'] = $month;
        $params['cvv'] = $request->cvv;

        $client = new Cardinity();

        $response = $client->execPayment($params);

        return view("response", compact('response'));
    }
}
