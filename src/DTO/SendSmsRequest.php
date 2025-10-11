<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;

class SendSmsRequest
{
    public string|array $to;
    public string $message;
    public ?string $callback = null;
    public ?string $from = null;
    public ?string $sender = null;
    public int $template = 0;

    public function __construct(array $data)
    {
        $this->validate($data);
        
        $this->to = $data['to'];
        $this->message = $data['message'];
        $this->callback = $data['callback'] ?? null;
        $this->from = $data['from'] ?? null;
        $this->sender = $data['sender'] ?? null;
        $this->template = $data['template'] ?? 0;
    }

    private function validate(array $data): void
    {
        if (empty($data['to'])) {
            throw new ValidationException('Recipient phone number is required');
        }

        if (empty($data['message'])) {
            throw new ValidationException('Message is required');
        }

        if (strlen($data['message']) > 1600) {
            throw new ValidationException('Message must not exceed 1600 characters');
        }

        if (is_array($data['to'])) {
            foreach ($data['to'] as $phone) {
                $this->validatePhoneNumber($phone);
            }
        } else {
            $this->validatePhoneNumber($data['to']);
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
        return array_filter([
            'to' => $this->to,
            'message' => $this->message,
            'callback' => $this->callback,
            'from' => $this->from,
            'sender' => $this->sender,
            'template' => $this->template,
        ], fn($value) => $value !== null);
    }
}