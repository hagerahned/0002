<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserLoginResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request){
        
        if(Auth::attempt($request->only(['email','password']))){
            $user = Auth::user();
            $user->token = $user->createToken($user->role,[$user->role])->plainTextToken;
            return ApiResponse::sendResponse('Login successful', new UserLoginResource($user), true);
        }
        return ApiResponse::sendResponse('Invalid credentials', [], false);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse('Logged out successfully', [], true);
    }
}
