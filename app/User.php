<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identity_number', 'name', 'email', 'phone', 'password', 'photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'user_id', 'id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->using(Queue::class)
            ->withPivot('id', 'estimated_time_serve', 'start_time_serve', 'finish_time_serve')
            ->withTimestamps();
    }
}
