<?php

namespace Dizatech\Transaction\Abstracts;

use Dizatech\Transaction\Contracts\DriverInterface;
use Dizatech\Transaction\Traits\DatabaseActions;

abstract class Driver implements DriverInterface
{
    use DatabaseActions;

    public $driver;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    abstract public function init($amount, $orderId, $callbackUrl,  $detail = []);

    abstract public function verify($request);

    public function getInformation() {
        return config('dizatech_transaction.information')[$this->driver]['constructor'];
    }
}
