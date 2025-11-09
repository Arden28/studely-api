<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Cache;

class RegistrationCache
{
    protected string $prefix = 'reg_init:';

    public function put(string $phone, array $payload, int $ttlMinutes = 15): void
    {
        Cache::put($this->prefix.$phone, $payload, now()->addMinutes($ttlMinutes));
    }

    public function get(string $phone): ?array
    {
        return Cache::get($this->prefix.$phone);
    }

    public function forget(string $phone): void
    {
        Cache::forget($this->prefix.$phone);
    }
}
