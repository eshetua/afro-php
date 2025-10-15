<?php

namespace Afromessage\Laravel\Services;

use Afromessage\Laravel\Contracts\OtpServiceInterface;
use Afromessage\Laravel\DTO\SendOtpRequest;
use Afromessage\Laravel\DTO\VerifyOtpRequest;
use Afromessage\Laravel\Exceptions\AfroMessageException;

class OtpService implements OtpServiceInterface
{
    public function __construct(private AfroMessageService $client) {}

    public function send(SendOtpRequest $request): array
    {
        try {
            $payload = array_merge($this->client->getDefaultSender(), $request->toArray());
            
            $response = $this->client->getHttpClient()->get('challenge', [
                'query' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new AfroMessageException('OTP challenge failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function verify(VerifyOtpRequest $request): array
    {
        try {
            $response = $this->client->getHttpClient()->get('verify', [
                'query' => $request->toArray(),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new AfroMessageException('OTP verification failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}