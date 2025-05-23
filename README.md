# Laravel Payment Package For Iranian Bank Gateways
[![Latest Version on Packagist](https://img.shields.io/packagist/v/dizatech/transaction.svg?style=flat-square)](https://packagist.org/packages/dizatech/transaction)
[![GitHub issues](https://img.shields.io/github/issues/dizatech/transaction?style=flat-square)](https://github.com/dizatech/transaction/issues)
[![GitHub stars](https://img.shields.io/github/stars/dizatech/transaction?style=flat-square)](https://github.com/dizatech/transaction/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/dizatech/transaction?style=flat-square)](https://github.com/dizatech/transaction/network)
[![Total Downloads](https://img.shields.io/packagist/dt/dizatech/transaction.svg?style=flat-square)](https://packagist.org/packages/dizatech/transaction)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dizatech/transaction/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/dizatech/transaction/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/dizatech/transaction/badges/build.png?b=main)](https://scrutinizer-ci.com/g/dizatech/transaction/build-status/main)
[![GitHub license](https://img.shields.io/github/license/dizatech/transaction?style=flat-square)](https://github.com/dizatech/transaction/blob/master/LICENSE)

This is a Laravel Package for Payment Gateway Integration.

## <g-emoji class="g-emoji" alias="gem" fallback-src="https://github.githubassets.com/images/icons/emoji/unicode/1f48e.png">💎</g-emoji> List of available drivers

- [parsian](https://www.pec.ir/)

- [pasargad](https://bpi.ir/)

- [zarinpal](https://www.zarinpal.com/)

- [sadad](https://sadadpsp.ir/)


## How to install and config [dizatech/transaction](https://github.com/dizatech/transaction) package?

#### <g-emoji class="g-emoji" alias="arrow_down" fallback-src="https://github.githubassets.com/images/icons/emoji/unicode/2b07.png">⬇️</g-emoji> Installation

```bash

composer require dizatech/transaction

```

#### Publish Config file

```php

php artisan vendor:publish --tag=dizatech_transaction

```

#### Migrate tables, to add transactions table to database

```php

php artisan migrate

```

#### <g-emoji class="g-emoji" alias="book" fallback-src="https://github.githubassets.com/images/icons/emoji/unicode/1f4d6.png">📖</g-emoji> How to use exists drivers from package

- Set the configs in /config/dizatech_transaction.php

- Use this sample code for Request Payment 

    ```php
    <?php
  
    // Parsian Driver
    $transaction = Transaction::driver('parsian')
            ->amount(2000)
            ->orderId(2000)
            ->callbackUrl('callback_parsian')
            ->detail(['auto_redirect' => false]) // if we want to get {token, url} and not auto redirect to Bank Gateway.
            ->pay();
  
    // Pasargad Driver
    $transaction = Transaction::driver('pasargad')
            ->amount(2000)
            ->orderId(2000)
            ->callbackUrl('callback_pasargad')
            ->detail(['auto_redirect' => false]) // if we want to get {token, url} and not auto redirect to Bank Gateway.
            ->pay();

    ```
  
- Use this sample code for Verify Payment

    ```php
    <?php

    // Parsian Driver, that use POST type
    Route::post('/callback_parsian', function () {
        $verify = Transaction::driver('parsian')->request(request()->all())->verify();
    });
    
    // Pasargad Driver, that use GET type
    Route::get('/callback_pasargad', function () {
        $verify = Transaction::driver('pasargad')->request(request()->all())->verify();
    });

    ```

- Use this Trait in you'r Model (for example Payment, Invoice, Order, ...) that has many transactions and has relation with Transaction Model

    ```php
  <?php
  
    // Use the Trait
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
  
    use Dizatech\Transaction\Traits\HasTransaction;
    
    class Order extends Model
    {
        use HasFactory, HasTransaction;
    }
  
    // After add the Trait we can use this relations
    $order->transactions; // get the all transactions for this order
    $order->pendingTransactions; // get the pending transactions for this order
    $order->successfulTransactions; // get the successful transactions for this order
    $order->failedTransactions; // get the failed transactions for this order
    $order->refundedTransactions; // get the refunded transactions for this order
    
    ```
- Get the parent of a transaction or this transaction belongs to which model

    ```php
  <?php
  
    // Set the namespace of your model in /config/dizatech_transaction.php
    'model' => 'App\Models\Order',

    // Use relation for get a parent of this transaction
    $transaction->parent;
    ```
  
- Transaction model's data and appends

    ```php
  <?php
  
    // Default
    $transaction->id;
    $transaction->order_id;
    $transaction->amount;
    $transaction->driver;
    $transaction->status;
    $transaction->ref_no;
    $transaction->token;
    $transaction->created_at;
    $transaction->updated_at;
    // Appends
    $transaction->gateway; // Label of driver 
    $transaction->toman; // Get price to taman (convert rial to toman)
    $transaction->status_label; // Label of status
    ```

#### Requirements:

- PHP v7.0 or above
- Laravel v7.0 or above

