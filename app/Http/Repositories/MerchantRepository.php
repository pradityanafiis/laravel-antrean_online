<?php

namespace App\Http\Repositories;

use App\Category;
use App\Merchant;
use Illuminate\Http\Request;

class MerchantRepository
{
    private Merchant $merchant;

    public function __construct()
    {
        $this->merchant = new Merchant();
    }

    public function getAll()
    {
        return $this->merchant
            ->with('services', 'businesshours')
            ->latest()
            ->get();
    }

    public function findByName($name)
    {
        return $this->merchant
            ->with('services', 'businesshours')
            ->where('name', 'like', "%$name%")
            ->latest()
            ->get();
    }

    public function findByCategory(Category $category)
    {
        return $this->merchant
            ->with('services', 'businesshours')
            ->where('category_id', '=', $category->id)
            ->latest()
            ->get();
    }

    public function findById($id)
    {
        return $this->merchant
            ->with('services', 'businesshours')
            ->find($id)
            ->first();
    }

    public function findByUser()
    {
        return $this->merchant
            ->with('services', 'businesshours')
            ->where('user_id', auth()->user()->id)
            ->first();
    }

    public function store(Request $request)
    {
        return auth()->user()->merchant()->create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone' => $request->phone,
            'photo' => $request->photo
        ]);
    }

    public function update(Request $request)
    {
        return $this->getCurrentMerchant()->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone' => $request->phone,
            'photo' => $request->photo
        ]);
    }

    public function getCurrentMerchant()
    {
        return auth()->user()->merchant;
    }

}
