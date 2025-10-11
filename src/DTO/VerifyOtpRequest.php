<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;

class VerifyOtpRequest
{
    public string $to;
    public string $code;

    public function __construct(array $data)
    {
        $this->validate($data);
        
        $this->to = $data['to'];
        $this->code = $data['code'];
    }

    private function validate(array $data): void
    {
        if (empty($data['to'])) {
            throw new ValidationException('Recipient phone number is required');
        }

        if (empty($data['code'])) {
            throw new ValidationException('OTP code is required');
        }

        $this->validatePhoneNumber($data['to']);

        if (!preg_match('/^\d{4,8}$/', $data['code'])) {
            throw new ValidationException('OTP code must be 4-8 digits');
        }
    }

    private function validatePhoneNumber(string $phone): void
    {
        $cleanedPhone = preg_replace('/\s+/', '', $phone);
        
        if (!preg_match('/^(\+\d+|\d+)$/', $cleanedPhone)) {
            throw new ValidationException('Phone number must be in E.164 format or valid digits');
        }
    }

    public function toArray(): array
    {
        return [
            'to' => $this->to,
            'code' => $this->code,
        ];
    }
}