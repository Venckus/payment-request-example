<?php

namespace App\Services;

use Cardinity\Client;
use Cardinity\Method\Payment;

class Cardinity
{

    /**
     * private property for client
     */
    private $client;


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
        $method = new Payment\Create([
            'amount' => (float) $params['amount'],
            'currency' => 'EUR',
            'settle' => false,
            'description' => '3d-pass', 
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
        $payment = $this->exec($method);

        return $this->finalize($payment);
    }


    /**
     * Finalize witn 3D 
     */
    private function finalize($payment)
    {
        $paymentId = $payment->getId();
        
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
        } catch (Exception\ValidationFailed $exception) {
            /** @type Cardinity\Method\Payment\Payment */
            $payment = $exception->getResult();
            $result['status'] = $payment->getStatus(); // value will be 'declined'
            $result['error'] = $exception->getErrors(); // list of errors occured
        }
        return $result;
    }
}