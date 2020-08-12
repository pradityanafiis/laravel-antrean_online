<?php

namespace App\Http\Repositories;

use App\BusinessHour;
use App\Merchant;

class BusinessHourRepository
{
    private BusinessHour $businessHour;

    public function __construct()
    {
        $this->businessHour = new BusinessHour();
    }

    public function store(Merchant $merchant, $businessHour) {
        return $merchant->businesshours()->create([
            'day_of_week' => $businessHour['day_of_week'],
            'open_time' => $businessHour['open_time'],
            'close_time' => $businessHour['close_time']
        ]);
    }

    public function destroy() {
        return $this->businessHour->where('merchant_id', auth()->user()->merchant->id)->delete();
    }
}
