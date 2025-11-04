<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequestRequest;
use App\Http\Requests\Auth\OtpVerifyRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private OtpService $otp) {}

    /** POST /api/v1/login */
    public function login(LoginRequest $req)
    {
        // You can support phone login later; here we use email+password
        $creds = ['email' => $req->input('email'), 'password' => $req->input('password')];

        if (!empty($req->phone)) {
            // Optional: map phone → user email, or implement custom provider
            $user = User::where('phone', $req->phone)->first();
            if (!$user) return response()->json(['message'=>'Invalid credentials'], 422);
            $creds['email'] = $user->email;
        }

        if (!Auth::attempt(['email' => $creds['email'], 'password' => $creds['password']])) {
            return response()->json(['message'=>'Invalid credentials'], 422);
        }

        $req->session()->regenerate();

        return response()->json([
            'message' => 'ok',
            'user'    => [
                'id'    => $req->user()->id,
                'name'  => $req->user()->name,
                'email' => $req->user()->email,
                'tenant_id' => $req->user()->tenant_id,
                'roles' => $req->user()->getRoleNames(),
            ],
        ]);
    }

    /** POST /api/v1/logout */
    public function logout(Request $req)
    {
        Auth::guard('web')->logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return response()->json(['message'=>'ok']);
    }

    /** POST /api/v1/otp/request  (channel, identifier, purpose) */
    public function otpRequest(OtpRequestRequest $req)
    {
        $this->otp->request(
            $req->channel,
            $req->identifier,
            $req->purpose,
            ttlMinutes: (int) (config('auth.otp_ttl', 10)),
            digits: (int) (config('auth.otp_digits', 6))
        );

        return response()->json(['message'=>'otp_sent']);
    }

    /** POST /api/v1/otp/verify  (channel, identifier, purpose, code) */
    public function otpVerify(OtpVerifyRequest $req)
    {
        $ok = $this->otp->verify(
            $req->channel,
            $req->identifier,
            $req->purpose,
            $req->code,
            maxAttempts: (int) (config('auth.otp_max_attempts', 5))
        );

        return $ok
            ? response()->json(['message'=>'verified'])
            : response()->json(['message'=>'invalid_or_expired_code'], 422);
    }
}
