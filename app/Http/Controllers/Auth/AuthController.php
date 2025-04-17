<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordOtpRequest;
use App\Http\Requests\SendForgetPasswordOTP;
use App\Http\Requests\SendForgetPasswordOTPRequest;
use App\Http\Resources\UserLoginResource;
use App\Mail\SendOTP;
use App\Models\User;
use Ichtrojan\Otp\Models\Otp as ModelsOtp;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        if (Auth::attempt($request->only(['email', 'password']))) {
            $user = Auth::user();
            $user->token = $user->createToken($user->role, [$user->role])->plainTextToken;
            return ApiResponse::sendResponse('Login successful', new UserLoginResource($user), true);
        }
        return ApiResponse::sendResponse('Invalid credentials', [], false);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse('Logged out successfully', [], true);
    }

    public function forgetPassword(SendForgetPasswordOTPRequest $request)
    {
        $email = $request->input('email');
        $otp = (new Otp)->generate('uccd@support.com', 'numeric', 6, 30);
        Mail::to($email)->sendNow(new SendOTP($otp->token));
        return ApiResponse::sendResponse('OTP Sent Successfuly', [], true);
    }
    public function resetPassword(ResetPasswordOtpRequest $request)
    {
        $email = $request->input('email');
        $otp = ModelsOtp::where('token',$request->input('otp'))->first();
        $password = $request->input('password');

        // check if otp exists
        if(!$otp){
            return ApiResponse::sendResponse('Invalid or expired OTP',[],false);
        }
        // 1. Validate OTP
        $otpValidator = new Otp;
        if (!$otpValidator->validate($email, $otp->token)) {
            return ApiResponse::sendResponse('Invalid or expired OTP',[],false);
        }

        // 2. Find the user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return ApiResponse::sendResponse('User not found',[],false);
        }

        // 3. Update the password
        $user->password = Hash::make($password);
        $user->save();

        // Delete opt after change password
        $otp->delete();
        // 5. Return response
        return ApiResponse::sendResponse('Password reset successfully.',[],true);
    }
}
