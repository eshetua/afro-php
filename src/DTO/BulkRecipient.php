<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;

class BulkRecipient
{
    public string $to;
    public string $message;

    public function __construct(array $data)
    {
        $this->validate($data);
        
        $this->to = $data['to'];
        $this->message = $data['message'];
    }

    private function validate(array $data): void
    {
        if (empty($data['to'])) {
            throw new ValidationException('Recipient phone number is required');
        }

        if (empty($data['message'])) {
            throw new ValidationException('Message is required');
        }

        $this->validatePhoneNumber($data['to']);
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
            'message' => $this->message,
        ];
    }
}