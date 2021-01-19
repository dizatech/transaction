<?php

namespace Dizatech\Transaction\Contracts;

interface DriverInterface
{
    public function init($amount, $orderId, $callbackUrl, $detail = []);

    public function verify($request);
}
