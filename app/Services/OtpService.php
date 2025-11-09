<?php

namespace App\Services;

use App\Models\OtpToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\Sms\SmsSender;

class OtpService
{
    public function __construct(private ?SmsSender $sms = null) {}

    public function request(string $channel, string $identifier, string $purpose, int $ttlMinutes = 10, int $digits = 6): void
    {
        $code = str_pad((string)random_int(0, (10 ** $digits) - 1), $digits, '0', STR_PAD_LEFT);

        // invalidate old active tokens for same identifier/purpose
        OtpToken::where('identifier',$identifier)
            ->where('purpose',$purpose)
            ->where('consumed',false)
            ->update(['consumed'=>true]);

        OtpToken::create([
            'channel'    => $channel,
            'identifier' => $identifier,
            'purpose'    => $purpose,
            'code_hash'  => Hash::make($code),
            'expires_at' => Carbon::now()->addMinutes($ttlMinutes),
            'attempts'   => 0,
            'consumed'   => false,
        ]);

        if ($channel === 'sms' && $this->sms) {
            $this->sms->send($identifier, "Your verification code is: {$code}. It expires in {$ttlMinutes} minutes.");
        }
        // For email channel, you can integrate Mail later. For dev, log it:
        if ($channel === 'email') {
            logger()->info("OTP for {$identifier}: {$code}. It expires in {$ttlMinutes} minutes.");
        }
    }

    public function verify(string $channel, string $identifier, string $purpose, string $code, int $maxAttempts = 5): bool
    {
        $token = OtpToken::where('channel',$channel)
            ->where('identifier',$identifier)
            ->where('purpose',$purpose)
            ->where('consumed',false)
            ->where('expires_at','>', now())
            ->latest('id')
            ->first();

        if (!$token) return false;

        if ($token->attempts >= $maxAttempts) {
            // lock/consume to prevent brute force
            $token->consumed = true;
            $token->save();
            return false;
        }

        $token->attempts += 1;
        $ok = Hash::check($code, $token->code_hash);

        if ($ok) {
            $token->consumed = true;
        }
        $token->save();

        return $ok;
    }
}
