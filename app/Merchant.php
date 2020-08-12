<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $table = 'merchants';

    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id',
        'user_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'phone',
        'photo'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'merchant_id', 'id');
    }

    public function businessHours()
    {
        return $this->hasMany(BusinessHour::class, 'merchant_id', 'id');
    }
}
