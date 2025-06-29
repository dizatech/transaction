<?php

namespace Dizatech\Transaction\Drivers;

use Dizatech\SamanIpg\SamanIpg;
use Dizatech\Transaction\Abstracts\Driver;
use Dizatech\Transaction\Models\Transaction;

class Saman extends Driver
{
    public function init($amount, $orderId, $callbackUrl, $detail = [])
    {
        // Create new transaction
        $transaction = $this->createNewTransaction($orderId, $amount);

        // Create object from Sadad Driver
        $class = new SamanIpg($this->getInformation());

        $result = $class->requestPayment(
            amount: $amount,
            order_id: $transaction->id,
            redirect_url: $callbackUrl
        );

        if (!isset($detail['auto_redirect']) || $detail['auto_redirect'] == true) {
            echo "<form method='post' action='https://sep.shaparak.ir/OnlinePG/OnlinePG' id='saman_redirect_form' style='display: none;'>
                <input type='text' name='token' value='{$result->token}'>
                <button type='submit'>Send</button>
            </form>
            <script>window.addEventListener('load', function () {document.getElementById('saman_redirect_form').submit()})</script>";
            exit;
        }

        return $result;
    }

    public function verify($request)
    {
        $class = new SamanIpg($this->getInformation());
        if (isset($request['State']) && $request['State'] == 'OK') {
            $transaction = Transaction::whereId($request['ResNum'])->first();
            $result = $class->verify(amount: intval($transaction->amount), ref_number: $request['RefNum']);

            if ($result->status == 'success') {
                $this->updateTransactionData($transaction->id, ['status' => 'successful', 'ref_no' => $result->ref_no, 'token' => $result->token]);
            } else {
                $this->updateTransactionData($transaction->id, ['status' => 'failed']);
            }

            return $result;
        }

        $this->updateTransactionData($request['ResNum'], ['status' => 'failed']);
        return (object)[
            'status'        => 'error',
            'message'       => 'پرداخت با خطا مواجه شد.',
        ];
    }
}
