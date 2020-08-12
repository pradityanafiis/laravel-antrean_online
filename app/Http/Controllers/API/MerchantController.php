<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Repositories\BusinessHourRepository;
use App\Http\Repositories\CategoryRepository;
use App\Http\Repositories\MerchantRepository;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    private CategoryRepository $categoryRepository;
    private MerchantRepository $merchantRepository;
    private BusinessHourRepository $businessHourRepository;
    private $path;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->merchantRepository = new MerchantRepository();
        $this->businessHourRepository = new BusinessHourRepository();
        $this->path = public_path('merchant_photos');
    }

    public function getAll()
    {
        return response()->json([
            'error' => false,
            'message' => 'Success, merchant list retrieved',
            'data' => $this->merchantRepository->getAll()
        ]);
    }

    public function findByName(Request $request)
    {
        $data = $this->merchantRepository->findByName($request->name);
        if ($data->isNotEmpty()) {
            return response()->json([
                'error' => false,
                'message' => 'Success, merchant list retrieved',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, merchant not found',
                'data' => $data
            ]);
        }
    }

    public function findByCategory(Request $request)
    {
        $category = $this->categoryRepository->findByName($request->category);
        $data = $this->merchantRepository->findByCategory($category);
        if ($data->isNotEmpty()) {
            return response()->json([
                'error' => false,
                'message' => 'Success, merchant list retrieved',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, merchant not found',
                'data' => $data
            ]);
        }
    }

    public function findByUser()
    {
        $data = $this->merchantRepository->findByUser();
        if (!empty($data)) {
            return response()->json([
                'error' => false,
                'message' => 'Success, merchant retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to retrieve merchant!',
                'data' => $data
            ]);
        }
    }

    public function findById(Request $request) {
        $data = $this->merchantRepository->findById($request);
        if (!empty($data)) {
            return response()->json([
                'error' => false,
                'message' => 'Success, merchant retrieved!',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to retrieve merchant!',
                'data' => $data
            ]);
        }
    }

    public function store(Request $request)
    {
        $merchant = $this->merchantRepository->store($request);

        foreach ($request->businesshours as $businesshour) {
            $this->businessHourRepository->store($merchant, $businesshour);
        }

        if ($merchant->exists) {
            return response()->json([
                'error' => false,
                'message' => 'Success, merchant created!',
                'data' => $this->merchantRepository->getCurrentMerchant()
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to create merchant!',
                'data' => null
            ]);
        }
    }

    public function update(Request $request)
    {
        $update = $this->merchantRepository->update($request);
        $merchant = $this->merchantRepository->getCurrentMerchant();
        if ($update) {
            return response()->json([
                'error' => false,
                'message' => 'Success, merchant updated!',
                'data' => $merchant
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to update merchant!',
                'data' => $merchant
            ]);
        }
    }
}
