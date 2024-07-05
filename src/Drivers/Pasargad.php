<?php

namespace Dizatech\Transaction\Drivers;

use Dizatech\PasargadIpg\PasargadIpg;
use Dizatech\Transaction\Abstracts\Driver;
use stdClass;

class Pasargad extends Driver
{
    public function init($amount, $orderId, $callbackUrl, $detail = [])
    {
        // Create new transaction
        $transaction = $this->createNewTransaction($orderId, $amount);

        // Create object from Pasargad Driver
        $class = new PasargadIpg($this->getInformation());
        $class->verifySSL(config('dizatech_transaction.information')['pasargad']['options']['verifySSL']);

        $result = $class->purchase(
            amount: $amount,
            invoice_number: $transaction->id,
            invoice_date: $transaction->created_at->format('Y/m/d H:i:s'),
            redirect_address: $callbackUrl
        );

        if (isset($detail['auto_redirect']) && $detail['auto_redirect'] == false && $result->status == 'success') {
            $result->token  = $result->url_id;
            $result->url    = $result->payment_url;
            return $result;
        } elseif ($result->status == 'success') {
            $this->updateTransactionData($transaction->id, ['token' => $result->url_id]);
            header('Location: ' . $result->payment_url);
            die();
        }

        return $result;
    }

    public function verify($request)
    {
        $class = new PasargadIpg($this->getInformation());
        $class->verifySSL(config('dizatech_transaction.information')['pasargad']['options']['verifySSL']);

        if ($request['status'] == 'success') {
            $inquiry = $class->inquiry(invoice_number: $request['invoiceId']);
            if ($inquiry->status == 'success' && $inquiry->payment_status == 'success') {
                $verification_result = $class->verify(invoice_number: $request['invoiceId'], url_id: $inquiry->url_id);

                $result = new stdClass();
                if ($verification_result->status == 'success') {
                    $result->status = 'success';
                    $result->shaparak_ref = $verification_result->reference_number;
                    $this->updateTransactionData($request['invoiceId'], ['status' => 'successful', 'ref_no' => $verification_result->reference_number]);
                } else {
                    $result->status = 'error';
                    $result->message = 'پرداخت با خطا مواجه شد.';
                    $this->updateTransactionData($request['invoiceId'], ['status' => 'failed', 'ref_no' => $verification_result->reference_number]);
                }

                return $result;
            }
        }

        $this->updateTransactionData($request['invoiceId'], ['status' => 'failed']);
        return (object)[
            'status'        => 'error',
            'message'       => 'پرداخت با خطا مواجه شد.',
        ];
    }
}
