<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redis;

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
    public function process(Request $request, Cardinity $cardinity_service)
    {
        $date = explode(' ', $request->expdate);

        $params = $request->input();
        // dd($request);
        foreach ($date as $d) {

            if (strlen($d) == 4) {

                $year = $d;
            }
            elseif (strlen($d) == 2) {

                $month = $d;
            }
        }
        $params['year'] = $year;
        $params['month'] = $month;

        $response = $cardinity_service->execPayment($params);

        if ($response->getStatus() == 'pending') {

            $callback =  config('services.cardinity.url') . '/result'; // //'https://a588c9be2db6.ngrok.io'
            
            $url_id = $response->url_id;
            
            $attr = $response->getAuthorizationInformation();
            
            return view("acs", compact('attr', 'url_id', 'callback'));

        } else {

            $response['error'] = 'Payment was not confirmed';

            return view("response", compact('response'));
        }

    }


    /**
     * Response page
     */
    public function result(Request $request, Cardinity $cardinity_service)
    {
        $result = $request->all();
        
        $cached_payment = Redis::hgetall('urlid_' . $result['MD']);

        if ($result['PaRes'] == '3d-pass' && !empty($cached_payment)) {

            $finalized_status = $cardinity_service->finalize($cached_payment['payment_id']);
            
            if ($finalized_status == 'approved') {

                $response['message'] = 'payment was successfull.';
                
                $response = array_merge($response, $cached_payment);

            } else {

                $response['error'] = 'payment verification FAILED at finalize stage.';
            }

        } else {

            $response['error'] = 'payment verification FAILED at 3D verification.';
        }
        return view("response", compact('response'));
    }
}
