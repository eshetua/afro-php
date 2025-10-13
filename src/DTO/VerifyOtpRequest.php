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

        // Remove strict numeric validation and allow any OTP format
        $this->validateOtpCode($data['code']);
    }

    private function validatePhoneNumber(string $phone): void
    {
        $cleanedPhone = preg_replace('/\s+/', '', $phone);
        
        if (!preg_match('/^(\+\d+|\d+)$/', $cleanedPhone)) {
            throw new ValidationException('Phone number must be in E.164 format or valid digits');
        }
    }

    private function validateOtpCode(string $code): void
    {
        // Remove any whitespace
        $cleanedCode = preg_replace('/\s+/', '', $code);
        
        // Check if code is not empty after cleaning
        if (empty($cleanedCode)) {
            throw new ValidationException('OTP code cannot be empty');
        }

        if (strlen($cleanedCode) < 4) {
            throw new ValidationException('OTP code must be at least 4 character');
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