<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
    private UserRepository $userRepository;
    private $path;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->path = public_path('user_photos');
    }

    public function register(Request $request)
    {
        if ($this->userRepository->findByEmail($request->email)) {
            return response()->json([
                'error' => true,
                'message' => 'Failed, email already exist.',
                'data' => null
            ]);
        }

        if ($this->userRepository->findByIdentityNumber($request->identity_number)) {
            return response()->json([
                'error' => true,
                'message' => 'Failed, identity number already exist.',
                'data' => null
            ]);
        }

        if ($this->userRepository->store($request)) {
            $user = $this->userRepository->findByEmail($request->email);
            $token = $user->createToken('antrean-online-token')->plainTextToken;
            $user->setAttribute('token', $token);

            return response()->json([
                'error' => false,
                'message' => 'Success, user created.',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to create user.',
                'data' => null
            ]);
        }
    }

    public function login(Request $request)
    {
        $user = $this->userRepository->findByEmail($request->email);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $this->userRepository->setFirebaseToken($request->email, $request->firebase_token);
                $token = $user->createToken('antrean-online-token')->plainTextToken;
                $user->setAttribute('token', $token);
                $user->setAttribute('has_merchant', $user->merchant()->exists());

                return response()->json([
                    'error' => false,
                    'message' => 'Login successfull',
                    'data' => $user
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Failed, wrong password.',
                    'data' => null
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, account not found.',
                'data' => null
            ]);
        }
    }

    public function changePassword(Request $request) {
        $user = $this->userRepository->getCurrentUser();
        if (Hash::check($request->current_password, $user->password)) {
            $this->userRepository->changePassword($request->new_password);
            return response()->json([
                'error' => false,
                'message' => 'Success, password updated!',
                'data' => $this->userRepository->getCurrentUser()
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, wrong current password!',
                'data' => null
            ]);
        }
    }

    public function logout()
    {
        $user = $this->userRepository->getCurrentUser();
        if ($this->userRepository->destroyToken($user)) {
            return response()->json([
                'error' => false,
                'message' => 'Success, user logged out.',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to logged out user.',
                'data' => $user
            ]);
        }
    }

    public function getAuthenticatedUser()
    {
        return response()->json([
            'error' => false,
            'message' => 'Success, authenticated user retrieved.',
            'data' => $this->userRepository->getCurrentUser()
        ]);
    }

    public function getAuthenticatedUserTokens()
    {
        $user = $this->userRepository->getCurrentUser();
        $tokens = $this->userRepository->getCurrentUserTokens($user);
        if ($tokens->isNotEmpty()) {
            return response()->json([
                'error' => false,
                'message' => 'Success, authenticated user tokens retrieved.',
                'data' => $tokens
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, token not found.',
                'data' => null
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $this->userRepository->getCurrentUser();
        if ($user->email != $request->email) {
            if ($this->userRepository->findByEmail($request->email)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Failed, email already exist!',
                    'data' => null
                ]);
            }
        }

        if ($request->has('photo')) {
            $update = $this->userRepository->updateWithPhoto($request);
        } else {
            $update = $this->userRepository->update($request);
        }

        if ($update) {
            return response()->json([
                'error' => false,
                'message' => 'Success, profile updated!',
                'data' => $this->userRepository->getCurrentUser()
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Failed, unable to update profile!',
                'data' => $this->userRepository->getCurrentUser()
            ]);
        }
    }
}
