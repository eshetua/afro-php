<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;
use Afromessage\Laravel\Validation\ValidatesAfromessageData;

class SendOtpRequest
{
    use ValidatesAfromessageData;

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
        $this->validateRequiredFields($data, ['to']);
        $this->validatePhoneNumber($data['to']);

        if (isset($data['len']) && $data['len'] < 1) {
            throw new ValidationException('OTP length must be at least 1 character');
        }

        if (isset($data['ttl'])) {
            $this->validateNumericRange($data['ttl'], 60, 3600, 'TTL');
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
            'len' => $this->len,
            't' => $this->t,
            'from' => $this->from,
            'sender' => $this->sender,
        ], fn($value) => $value !== null);
    }
}