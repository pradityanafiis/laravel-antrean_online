<?php

namespace App\Http\Repositories;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function store(Request $request)
    {
        return User::create([
            'identity_number' => $request->identity_number,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    }

    public function update(Request $request)
    {
        return $this->getCurrentUser()->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);
    }

    public function updateWithPhoto(Request $request)
    {
        return $this->getCurrentUser()->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo' => $request->photo
        ]);
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function findByIdentityNumber($identityNumber)
    {
        return User::where('identity_number', $identityNumber)->first();
    }

    public function getCurrentUserTokens(User $user)
    {
        return $user->tokens;
    }

    public function getCurrentUser()
    {
        return auth()->user();
    }

    public function destroyToken(User $user)
    {
        return $user->tokens()->delete();
    }

    public function changePassword($newPassword) {
        $this->getCurrentUser()->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
