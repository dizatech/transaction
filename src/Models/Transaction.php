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
        switch ($this->driver) {
            case 'pasargad':
                return 'بانک پاسارگاد';
            case 'parsian' :
                return 'بانک پارسیان';
            case 'zarinpal' :
                return 'زرین پال';
            case 'sadad' :
                return 'سداد';
            case 'mahamax' :
                return 'مهامکس';
            case 'saman' :
                return 'سامان';
            default:
                return $this->driver;
        }
    }

    public function getTomanAttribute()
    {
        return $this->amount / 10;
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'در انتظار پرداخت';
            case 'failed':
                return 'ناموفق';
            case  'refunded':
                return 'برگشت خورده';
            case 'successful':
                return 'موفقیت‌آمیز';
            default:
                return 'نامشخص';
        }
    }
}
