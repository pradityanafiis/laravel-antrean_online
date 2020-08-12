<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $primaryKey = 'id';

    protected $fillable = [
        'merchant_id',
        'name',
        'description',
        'quota',
        'interval',
        'max_scheduled_day'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(Queue::class)
            ->withPivot('id', 'estimated_time_serve', 'start_time_serve', 'finish_time_serve')
            ->withTimestamps();
    }
}
