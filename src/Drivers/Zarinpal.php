<?php

namespace Dizatech\Transaction\Drivers;

use Dizatech\Transaction\Abstracts\Driver;
use Dizatech\Transaction\Models\Transaction;
use Dizatech\ZarinpalIpg\ZarinpalIpg;

class Zarinpal extends Driver
{
    public function init($amount, $orderId, $callbackUrl, $detail = [])
    {
        // Create new transaction
        $transaction = $this->createNewTransaction($orderId, $amount);

        // Create object from Parsian Driver
        $class = new ZarinpalIpg($this->getInformation());
        $result = $class->getToken($amount, $transaction->id, $callbackUrl);

        if(isset($detail['auto_redirect']) && $detail['auto_redirect'] == false && $result->status == 'success') {
            $result->token  = $result->token;
            $result->url    = 'Location: https://www.zarinpal.com/pg/StartPay/' . $result->token;
            return $result;

        } elseif($result->status == 'success') {
            $this->updateTransactionData($transaction->id, ['token' => $result->token]);
            header( 'Location: https://www.zarinpal.com/pg/StartPay/' . $result->token );
            die();
        }

        return $result;
    }

    public function verify($request)
    {
        $class = new ZarinpalIpg($this->getInformation());

        $transaction = Transaction::whereToken( $request['Authority'] )->first();
        $result = $class->verifyRequest( intval($transaction->amount), $transaction->token );

        if ($result->status == 'success') {
            $this->updateTransactionData($transaction->id, ['status' => 'successful', 'ref_no' => $result->ref_id]);
        } else {
            $this->updateTransactionData($transaction->id, ['status' => 'failed']);
        }

        return $result;
    }
}
