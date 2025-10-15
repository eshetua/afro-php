<?php

namespace Afromessage\Laravel\Services;

use GuzzleHttp\Client;
use Afromessage\Laravel\Contracts\AfroMessageInterface;
use Afromessage\Laravel\Contracts\SmsServiceInterface;
use Afromessage\Laravel\Contracts\OtpServiceInterface;
use Afromessage\Laravel\Exceptions\AfroMessageException;

class AfroMessageService implements AfroMessageInterface
{
    private Client $httpClient;
    private SmsService $smsService;
    private OtpService $otpService;

    public function __construct(
        private string $token,
        private string $baseUrl = 'https://api.afromessage.com/api/',
        private ?string $senderId = null,
        private ?string $senderName = null,
        private int $timeout = 120
    ) {
        if (empty($this->token)) {
            throw new AfroMessageException('AfroMessage token is required');
        }

        $this->baseUrl = rtrim($this->baseUrl, '/') . '/';
        
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $this->smsService = new SmsService($this);
        $this->otpService = new OtpService($this);
    }

    public function sms(): SmsServiceInterface
    {
        return $this->smsService;
    }

    public function otp(): OtpServiceInterface
    {
        return $this->otpService;
    }

    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    public function getDefaultSender(): array
    {
        return [
            'from' => $this->senderId,
            'sender' => $this->senderName,
        ];
    }
}