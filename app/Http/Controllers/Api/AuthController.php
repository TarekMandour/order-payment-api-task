<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\Api\User\OtpRequest;
use App\Http\Requests\Api\User\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Whatsapp\WhatsAppApi;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller
{
    use ApiResponse;

    protected $whatsappService;

    public function __construct(WhatsAppApi $whatsappService) {
        $this->whatsappService = $whatsappService;
    }

    public function register (UserRequest $request) {

        $data = $request->validated();
        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $response = new UserResource($user);

        return $this->success(
            'Success',
            $response
        );

    }

    public function login (LoginRequest $request) {

        $data = $request->validated();

        if (!$user = JWTAuth::attempt($data)) {
            return $this->error('Invalid credentials', null, 401);
        }

        $user = User::where('phone', $request->phone)->first();

        $response = new UserResource($user);

        return $this->success(
            'Success',
            $response
        );

    }

    public function profile () {
        
        $response = new UserResource(auth('api')->user());

        return $this->success(
            'Success',
            $response
        );

    }

    public function checkPhone (Request $request) {

        if (User::where('phone', $request->phone)->exists()) {
            return $this->error('Phone number already registered', null, 409);
        }

        $storedOtp = Cache::get('otp_'.$request->phone);
        if ($storedOtp) {
            return $this->error('Wait 5 min to send again!', null, 429);
        }

        $otp = str_pad(random_int(0, 9999), 4, '0');

        Cache::put('otp_'.$request->phone, $otp, now()->addMinutes(5));

        $body = 'Verification Code:' . "\r\n";
        $body .= "*" . $otp . "*";

        $this->whatsappService->SendMsgOnly(0, $request->phone, $body);

        return $this->success('OTP sent successfully', $otp);

    }

    public function verifyOtp(OtpRequest $request)
    {

        $storedOtp = Cache::get('otp_'.$request->phone);

        if (!$storedOtp || $storedOtp != $request->otp) {
            return $this->error('Invalid or expired OTP');
        }

        Cache::forget('otp_'.$request->phone);

        return $this->success('OTP verified successfully', $request->phone);

    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->success('Logged out successfully');
    }
    
}
