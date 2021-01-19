<?php

namespace Dizatech\Transaction\Drivers;

use Dizatech\ParsianIpg\ParsianIpg;
use Dizatech\Transaction\Abstracts\Driver;

class Parsian extends Driver
{
    public function init($amount, $orderId, $callbackUrl, $detail = [])
    {
        // Create new transaction
        $transaction = $this->createNewTransaction($orderId, $amount);

        // Create object from Parsian Driver
        $class = new ParsianIpg($this->getInformation());
        $result = $class->paymentRequest($amount, $transaction->id, $callbackUrl);

        if( $result->status == 'success' ) {
            $this->updateTransactionData($transaction->id, ['token' => $result->token]);
            header( 'Location: https://pec.shaparak.ir/NewIPG/?Token=' . $result->token );
            die();
        } else {
            return $result;
        }
    }

    public function verify($request)
    {
        $class = new ParsianIpg($this->getInformation());

        $result = $class->confirmPayment( $request['Token'] );

        if ($result->status == 'success') {
            $this->updateTransactionData($request['OrderId'], ['status' => 'successful', 'ref_no' => $request['RRN']]);
        } else {
            $this->updateTransactionData($request['OrderId'], ['status' => 'failed']);
        }

        return $result;
    }
}
