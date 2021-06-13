<?php

namespace Dizatech\Transaction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'status', 'amount', 'driver', 'ref_no', 'token'];
    protected $appends = ['gateway','toman','status_label'];

    public function parent()
    {
        return $this->belongsTo( config('dizatech_transaction.model'), 'order_id' );
    }

    public function getGatewayAttribute()
    {
        $label = '';
        switch ($this->driver) {
            case 'pasargad':
                $label = 'بانک پاسارگاد';
                break;
            case 'parsian' :
                $label = 'بانک پارسیان';
                break;
            default:
                $label = 'نامشخص';

        }
        return $label;
    }

    public function getTomanAttribute()
    {
        return $this->amount / 10;
    }

    public function getStatusLabelAttribute()
    {
        $label = '';
        switch ($this->status) {
            case 'pending':
                $label = 'در انتظار پرداخت';
                break;
            case 'failed':
                $label = 'ناموفق';
                break;
            case  'refunded':
                $label = 'برگشت خورده';
                break;
            case 'successful':
                $label = 'موفقیت‌آمیز';
                break;
            case 'default':
                $label = 'نامشخص';
                break;
        }
        return $label;
    }
}
