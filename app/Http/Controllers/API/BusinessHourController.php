<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Repositories\BusinessHourRepository;
use Illuminate\Http\Request;

class BusinessHourController extends Controller
{
    private BusinessHourRepository $businessHourRepository;

    public function __construct()
    {
        $this->businessHourRepository = new BusinessHourRepository();
    }

    public function update(Request $request) {
        $this->businessHourRepository->destroy();

        $merchant = auth()->user()->merchant;
        foreach ($request->businesshours as $businesshour) {
            $this->businessHourRepository->store($merchant, $businesshour);
        }

        if (count($merchant->businesshours) > 0) {
            return response()->json([
                'error' => false,
                'message' => 'Success, business hours updated!',
                'data' => $merchant
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to update business hours!',
                'data' => $merchant
            ]);
        }
    }
}
