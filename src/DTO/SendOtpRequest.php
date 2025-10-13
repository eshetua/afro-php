<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;

class SendOtpRequest
{
    public string $to;
    public ?string $pr = null;
    public ?string $ps = null;
    public ?string $callback = null;
    public ?string $sb = null;
    public ?string $sa = null;
    public ?int $ttl = null;
    public ?int $len = null;
    public ?string $t = null;
    public ?string $from = null;
    public ?string $sender = null;

    public function __construct(array $data)
    {
        $this->validate($data);
        
        $this->to = $data['to'];
        $this->pr = $data['pr'] ?? null;
        $this->ps = $data['ps'] ?? null;
        $this->callback = $data['callback'] ?? null;
        $this->sb = $data['sb'] ?? null;
        $this->sa = $data['sa'] ?? null;
        $this->ttl = $data['ttl'] ?? null;
        $this->len = $data['len'] ?? null;
        $this->t = $data['t'] ?? null;
        $this->from = $data['from'] ?? null;
        $this->sender = $data['sender'] ?? null;
    }

    private function validate(array $data): void
    {
        if (empty($data['to'])) {
            throw new ValidationException('Recipient phone number is required');
        }

        $this->validatePhoneNumber($data['to']);

        // Remove length validation or make it more flexible
        if (isset($data['len'])) {
            // Allow any positive integer for length, or set reasonable limits if needed
            if ($data['len'] < 4) {
                throw new ValidationException('OTP length must be at least 1 character');
            }
        }

        if (isset($data['ttl']) && ($data['ttl'] < 60 || $data['ttl'] > 3600)) {
            throw new ValidationException('TTL must be between 60 and 3600 seconds');
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
            'pr' => $this->pr,
            'ps' => $this->ps,
            'callback' => $this->callback,
            'sb' => $this->sb,
            'sa' => $this->sa,
            'ttl' => $this->ttl,
            'len' => $data['len'] ?? null,
            't' => $this->t,
            'from' => $this->from,
            'sender' => $this->sender,
        ], fn($value) => $value !== null);
    }
}