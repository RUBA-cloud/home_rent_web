<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioVerifyService
{
    public function __construct(private readonly Client $twilio) {}

    public function start(string $phone, string $channel = 'sms')
    {
        return $this->twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
            ->verifications
            ->create($phone, $channel);
    }

    public function check(string $phone, string $code)
    {
        return $this->twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
            ->verificationChecks
            ->create([
                'to'   => $phone,
                'code' => $code,
            ]);
    }
}
