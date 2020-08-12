<?php

namespace App\Http\Repositories;

use App\Merchant;
use App\Service;
use Illuminate\Http\Request;

class ServiceRepository
{
    private Service $service;

    public function __construct()
    {
        $this->service = new Service();
    }

    public function findQuotaById($id)
    {
        return $this->service->find($id)->only('quota');
    }

    public function findById($id)
    {
        return $this->service->find($id);
    }

    public function findByMerchant()
    {
        return auth()->user()->merchant->services;
    }

    public function store(Merchant $merchant, Request $request)
    {
        return $merchant->services()->create([
            'name' => $request->name,
            'description' => $request->description,
            'quota' => $request->quota,
            'interval' => $request->interval,
            'max_scheduled_day' => $request->max_scheduled_day
        ]);
    }

    public function update(Request $request)
    {
        return $this->findById($request->id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'quota' => $request->quota,
            'interval' => $request->interval,
            'max_scheduled_day' => $request->max_scheduled_day
        ]);
    }
}
