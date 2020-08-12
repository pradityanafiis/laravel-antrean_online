<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $table = 'business_hours';

    protected $primaryKey = 'id';

    protected $fillable = [
        'merchant_id',
        'day_of_week',
        'open_time',
        'close_time'
    ];

    public function setOpenTimeAttribute($value) {
        $this->attributes['open_time'] = (new Carbon($value))->format('H:i');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
