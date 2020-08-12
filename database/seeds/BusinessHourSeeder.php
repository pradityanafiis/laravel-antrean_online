<?php

use Illuminate\Database\Seeder;
use App\BusinessHour;

class BusinessHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businessHours = [
            [
                'merchant_id' => 1,
                'day_of_week' => 'Monday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 1,
                'day_of_week' => 'Tuesday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 1,
                'day_of_week' => 'Wednesday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 1,
                'day_of_week' => 'Thursday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 1,
                'day_of_week' => 'Friday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 2,
                'day_of_week' => 'Monday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 2,
                'day_of_week' => 'Tuesday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 2,
                'day_of_week' => 'Wednesday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 2,
                'day_of_week' => 'Thursday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
            [
                'merchant_id' => 2,
                'day_of_week' => 'Friday',
                'open_time' => '08:00',
                'close_time' => '15:00'
            ],
        ];

        foreach ($businessHours as $businessHour) {
            BusinessHour::create($businessHour);
        }
    }
}
