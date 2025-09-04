<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class QrTokenService
{
    /** Generate a short-lived token for IN/OUT scans */
    public function generate(int $opportunityId, string $direction = 'in'): string
    {
        $payload = [
            'op'   => $opportunityId,
            'dir'  => $direction === 'out' ? 'out' : 'in',
            'ts'   => time(),
            'nonce'=> Str::random(8),
        ];
        return base64_encode(Crypt::encryptString(json_encode($payload)));
    }

    /** Validate token freshness (seconds) */
    public function isFresh(int $issuedAt, int $ttlSeconds): bool
    {
        return (time() - $issuedAt) <= $ttlSeconds;
    }
}
