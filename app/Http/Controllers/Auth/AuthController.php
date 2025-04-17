<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SendForgetPasswordOTP;
use App\Http\Requests\SendForgetPasswordOTPRequest;
use App\Http\Resources\UserLoginResource;
use App\Mail\SendOTP;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

    public function forgetPassword(SendForgetPasswordOTPRequest $request){
        $email = $request->input('email');
        $otp = (new Otp)->generate('uccd@support.com', 'numeric', 6, 30);
        Mail::to($email)->sendNow(new SendOTP($otp->token));
        return ApiResponse::sendResponse('OTP Sent Successfuly',[],true);
    }
    
}
