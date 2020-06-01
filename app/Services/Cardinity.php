<?php

namespace App\Services;

use Cardinity\Client;
use Cardinity\Method\Payment;

use Illuminate\Support\Str;
use App\PaymentConfirm;
use Illuminate\Support\Facades\Redis;

class Cardinity
{

    /**
     * private property for client
     */
    private $client;

    private $payment;


    /**
     * set client keys on initialization
     */
    public function __construct()
    {
        $this->client = Client::create([
            'consumerKey' => config('services.cardinity.key'),
            'consumerSecret' => config('services.cardinity.secret'),
        ]);
    }


    /**
     * execute request
     */
    public function execPayment($params)
    {
        $method = $this->preparePaymentMethod($params);
        $payment = $this->exec($method);
        
        if ($payment->getStatus() == 'pending') {

            $payment->url_id = $this->cachePayment($params, $payment->getId());
        }
        return $payment;
    }

    /**
     * prepare pament method
     */
    function preparePaymentMethod($params)
    {
        $method = new Payment\Create([
            'amount' => (float) $params['amount'],
            'currency' => 'EUR',
            'settle' => false,
            'description' => 'test real payment', //'3d-pass', 
            'order_id' => '12345678',
            'country' => 'LT',
            'payment_method' => Payment\Create::CARD,
            'payment_instrument' => [
                'pan' => $params['pan'],
                'exp_year' => (int) $params['year'],
                'exp_month' => (int) $params['month'],
                'cvc' =>  $params['cvv'],
                'holder' =>  $params['name']
            ],
        ]);
        return $method;
    }

    /**
     * save temporary data to Redis for 3D confirmation
     */
    public function cachePayment($params, $paymentId)
    {
        $url_id = Str::random(32);

        Redis::hmset('urlid_'.$url_id, [
            'user_name' => $params['name'],
            'amount' => $params['amount'],
            'payment_id' => $paymentId
        ]);
        // set expire time 5 minutes in seconds for this payment 3D processing
        Redis::command('expire', ['urlid_' . $url_id, 300]);

        return $url_id;
    }


    /**
     * Finalize with 3D 
     */
    public function finalize($paymentId)
    {
        $method = new Payment\Finalize( $paymentId, '3d-pass' );

        $final = $this->exec($method);

        return $final->getStatus();
    }

    private function exec($method)
    {
        try {
            $payment = $this->client->call($method);
            $status = $payment->getStatus();
            
            if($status == 'pending' || $status == 'approved') {

                return $payment; 
            }
        } catch (Exception\Declined $exception) {
            /** @type Cardinity\Method\Payment\Payment */
            $payment = $exception->getResult();
            $result['status'] = $payment->getStatus(); // value will be 'declined'
            $result['error'] = $exception->getErrors(); // list of errors occured
            return $result;
        } catch (Exception\ValidationFailed $exception) {
            /** @type Cardinity\Method\Payment\Payment */
            $payment = $exception->getResult();
            $result['status'] = $payment->getStatus(); // value will be 'declined'
            $result['error'] = $exception->getErrors(); // list of errors occured
            return $result;
        }
    }
}