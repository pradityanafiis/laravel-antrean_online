<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Queue extends Pivot
{
    protected $table = 'queues';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'service_id',
        'user_id',
        'queue_number',
        'schedule',
        'estimated_time_serve',
        'start_time_serve',
        'finish_time_serve',
        'status'
    ];

    public function service() {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
