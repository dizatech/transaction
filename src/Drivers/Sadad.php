<?php

namespace Dizatech\Transaction\Drivers;

// use Dizatech\PasargadIpg\PasargadIpg;

use Dizatech\SadadIpg\SadadIpg;
use Dizatech\Transaction\Abstracts\Driver;
// use stdClass;

class Sadad extends Driver
{
    public function init($amount, $orderId, $callbackUrl, $detail = [])
    {
        // Create new transaction
        $transaction = $this->createNewTransaction($orderId, $amount);

        // Create object from Sadad Driver
        $class = new SadadIpg($this->getInformation());

        $result = $class->requestPayment(
            amount: $amount,
            order_id: $transaction->id,
            redirect_url: $callbackUrl
        );

        if (!isset($detail['auto_redirect']) || $detail['auto_redirect'] == true) {
            echo "<form action='https://sadad.shaparak.ir/VPG/Purchase' id='sadad_redirect_form' style='display: none;'>
                <input type='text' name='token' value='{$result->token}'>
                <button type='submit'>Send</button>
            </form>
            <script>window.addEventListener('load', function () {document.getElementById('sadad_redirect_form').submit()})</script>";
            exit;
        }

        return $result;
    }

    public function verify($request)
    {
        $class = new SadadIpg($this->getInformation());

        if (isset($request['ResCode']) && $request['ResCode'] == 0 && isset($request['token'])) {
            $result = $class->verify(token: $request['token']);
            if ($result->status == 'success') {
                $this->updateTransactionData($request['OrderId'], ['status' => 'successful', 'ref_no' => $result->ref_no]);
            } else {
                $result->message = 'پرداخت با خطا مواجه شد.';
                $this->updateTransactionData($request['OrderId'], ['status' => 'failed']);
            }

            return $result;
        }

        $this->updateTransactionData($request['OrderId'], ['status' => 'failed']);
        return (object)[
            'status'        => 'error',
            'message'       => 'پرداخت با خطا مواجه شد.',
        ];
    }
}
