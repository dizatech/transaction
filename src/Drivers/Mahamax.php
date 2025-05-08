<?php

namespace Dizatech\Transaction\Drivers;

use Dizatech\Transaction\Abstracts\Driver;
use Dizatech\Transaction\Models\Transaction;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Mahamax extends Driver
{
    public function init($amount, $orderId, $callbackUrl, $detail = [])
    {
        // Create new transaction
        $transaction = $this->createNewTransaction($orderId, $amount);
        
        $gateway_info = $this->getInformation();
        $base_url = $gateway_info['base_url'];
        $client = new Client();

        try {
            $respone = $client->get(
                $base_url . '/request',
                [
                    'json'      => [
                        'consumer'          => 'fabreso',
                        'order_id'          => $transaction->id,
                        'payment_gateway'   => 'sadad',
                        'amount'            => $amount,
                        'callback_url'      => $callbackUrl,
                    ],
                    'headers'   => [
                        'accept'            => 'application/json'
                    ],
                ],
            );
            $result = json_decode($respone->getBody()->getContents());
            if (isset($result->request_id)) {
                if (!isset($detail['auto_redirect']) || $detail['auto_redirect'] == true) {
                    $transaction->token = $result->request_id;
                    $transaction->save();

                    header("Location: " . $base_url . "/start/" . $result->request_id);
                    exit;
                } else {
                    return $result;
                }
            } else {
                throw new Exception("Failed to retrieve request id from Mahamax for order id {$orderId}. details: " . json_encode($result));
            }
        } catch (Exception $e) {
            Log::debug("Payment request for order id {$orderId} failed with message: " . $e->getMessage());
        }
    }

    public function verify($request)
    {
        if (isset($request['status']) && $request['status'] == 'successful') {
            $gateway_info = $this->getInformation();
            $base_url = $gateway_info['base_url'];
            $client = new Client();
            try {
                $response = $client->get(
                    $base_url . "/verify",
                    [
                        'json'  => [
                            'consumer'          => 'fabreso',
                            'request_id'        => $request['request_id'],
                            'payment_gateway'   => 'sadad',
                            'ref_no'            => $request['ref_no'],
                        ],
                        'headers'   => [
                            'accept'            => 'application/json'
                        ],
                    ]
                );
                $result = json_decode($response->getBody()->getContents());

                return $result;
            } catch (Exception $e) {
                Log::debug('Mahamax tranasaction verification failed with message: ' . $e->getMessage());
                return (object)[
                    'status'        => 'error',
                    'message'       => 'پرداخت با خطا مواجه شد.',
                ];    
            }
        } else {
            return (object)[
                'status'        => 'error',
                'message'       => 'پرداخت با خطا مواجه شد.',
            ];
        }
        // dd($request);
        // if ($request->has('token')) {
        //     $transaction = Transaction::whereToken($request->token)->first();

        //     dd($request->all(), $transaction);
        // }
        // $class = new MahamaxIpg($this->getInformation());

        // if (isset($request['ResCode']) && $request['ResCode'] == 0 && isset($request['token'])) {
        //     $result = $class->verify(token: $request['token']);
        //     if ($result->status == 'success') {
        //         $this->updateTransactionData($request['OrderId'], ['status' => 'successful', 'ref_no' => $result->ref_no]);
        //     } else {
        //         $result->message = 'پرداخت با خطا مواجه شد.';
        //         $this->updateTransactionData($request['OrderId'], ['status' => 'failed']);
        //     }

        //     return $result;
        // }

        // $this->updateTransactionData($request['OrderId'], ['status' => 'failed']);
    }
}