<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Repositories\MerchantRepository;
use App\Http\Repositories\ServiceRepository;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private ServiceRepository $serviceRepository;

    public function __construct()
    {
        $this->serviceRepository = new ServiceRepository();
    }

    public function findByMerchant()
    {
        $data = $this->serviceRepository->findByMerchant();
        if (count($data) > 0) {
            return response()->json([
                'error' => false,
                'message' => 'Success, services retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, services not found!',
                'data' => $data
            ]);
        }
    }

    public function store(Request $request)
    {
        $merchant = auth()->user()->merchant;
        $data = $this->serviceRepository->store($merchant, $request);
        if (!empty($data)) {
            return response()->json([
                'error' => false,
                'message' => 'Success, service created!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to create service!',
                'data' => $data
            ]);
        }
    }

    public function update(Request $request)
    {
        $update = $this->serviceRepository->update($request);
        $service = $this->serviceRepository->findById($request->id);
        if ($update) {
            return response()->json([
                'error' => false,
                'message' => 'Success, service updated!',
                'data' => $service
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to update service!',
                'data' => $service
            ]);
        }
    }
}
