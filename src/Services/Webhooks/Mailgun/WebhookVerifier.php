<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Services\Webhooks\Mailgun;

class WebhookVerifier
{
    public function verify(string $signingKey, string $token, int $timestamp, string $signature): bool
    {
        // but because we have to do this in a queue I don't think it's a good idea to do so.
//        if (abs(time() - $timestamp) > 15) {
//            return false;
//        }

        return hash_equals(hash_hmac('sha256', $timestamp . $token, $signingKey), $signature);
    }
}
