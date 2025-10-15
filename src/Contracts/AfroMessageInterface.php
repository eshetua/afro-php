<?php

namespace Afromessage\Laravel\Contracts;

use Afromessage\Laravel\DTO\SendSmsRequest;
use Afromessage\Laravel\DTO\BulkSmsRequest;
use Afromessage\Laravel\DTO\SendOtpRequest;
use Afromessage\Laravel\DTO\VerifyOtpRequest;

interface AfroMessageInterface
{
    public function sms(): SmsServiceInterface;
    public function otp(): OtpServiceInterface;
}

interface SmsServiceInterface
{
    public function send(SendSmsRequest $request): array;
    public function sendGet(SendSmsRequest $request): array;
    public function bulkSend(BulkSmsRequest $request): array;
}

interface OtpServiceInterface
{
    public function send(SendOtpRequest $request): array;
    public function verify(VerifyOtpRequest $request): array;
}