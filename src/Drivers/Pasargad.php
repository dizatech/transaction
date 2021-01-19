<?php

namespace Dizatech\Transaction\Drivers;

use Dizatech\PasargadIpg\PasargadIpg;
use Dizatech\Transaction\Abstracts\Driver;

class Pasargad extends Driver
{
    public function init($amount, $orderId, $callbackUrl, $detail = [])
    {
        // Create new transaction
        $transaction = $this->createNewTransaction($orderId, $amount);

        // Create object from Pasargad Driver
        $class = new PasargadIpg($this->getInformation());
        $class->verifySSL(config('dizatech_transaction.information')['pasargad']['options']['verifySSL']);

        $result = $class->getToken( $amount, $transaction->id, $transaction->created_at->format('Y/m/d H:i:s'), $callbackUrl );

        if( $result->status == 'success' ) {
            $this->updateTransactionData($transaction->id, ['token' => $result->token]);
            header( 'Location: https://pep.shaparak.ir/payment.aspx?n=' . $result->token );
            die();
        } else {
            return $result;
        }
    }

    public function verify($request)
    {
        $class = new PasargadIpg($this->getInformation());
        $class->verifySSL(config('dizatech_transaction.information')['pasargad']['options']['verifySSL']);

        $check = $class->checkTransaction( $request['iN'], $request['iD'] );

        if ($check->status == 'success') {
            $transaction = $this->getTransaction($request['iN']);
            $result = $class->verifyTransaction($transaction->amount, $request['iN'], $request['iD']);

            if ($result->status == 'success') {
                $this->updateTransactionData($request['iN'], ['status' => 'successful', 'ref_no' => $request['tref']]);
            } else {
                $this->updateTransactionData($request['iN'], ['status' => 'failed', 'ref_no' => $request['tref']]);
            }
            return $result;
        }

        $this->updateTransactionData($request['iN'], ['status' => 'failed', 'ref_no' => $request['tref']]);
        return $check;
    }
}
