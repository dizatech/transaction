<?php

namespace Dizatech\Transaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'status', 'amount', 'driver', 'ref_no', 'token'];

    public function parent()
    {
        return $this->belongsTo( config('dizatech_transaction.model'), 'order_id' );
    }
}
