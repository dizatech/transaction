<?php

namespace Dizatech\Transaction\Core;

class TransactionManager
{
    protected $driver;
    protected $amount;
    protected $orderId;
    protected $callbackUrl;
    protected $detail;
    protected $config;
    protected $transaction;
    protected $request;

    public function __construct()
    {
        $this->config = config('dizatech_transaction');
    }

    public function pay()
    {
        $object = $this->fireDriver();
        return $object->init($this->amount, $this->orderId, $this->callbackUrl, $this->detail);
    }

    public function verify()
    {
        $object = $this->fireDriver();
        return $object->verify($this->request);
    }

    public function getDriver()
    {
        (is_null($this->driver))
            ? $driver = $this->config['default']
            : $driver = $this->driver;
        return $driver;
    }

    public function fireDriver()
    {
        $class = $this->config['drivers'][$this->getDriver()];
        return new $class($this->getDriver());
    }

    // Has parameter
    public function driver($driver = null)
    {
        $this->driver = $driver;
        return $this;
    }

    public function amount($amount)
    {
        $this->amount = (int) $amount;
        return $this;
    }

    public function orderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function callbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function request($request)
    {
        $this->request = $request;
        return $this;
    }

    public function detail($detail = [])
    {
        $this->detail = $detail;
        return $this;
    }
}
