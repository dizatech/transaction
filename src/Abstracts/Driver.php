<?php

namespace Dizatech\Transaction\Abstracts;

use Dizatech\Transaction\Contracts\DriverInterface;

abstract class Driver implements DriverInterface
{
    public $driver;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    abstract public function init($amount, $orderId, $callbackUrl,  $detail = []);

    abstract public function verify($request);
}
