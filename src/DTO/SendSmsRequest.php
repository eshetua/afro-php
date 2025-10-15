<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;
use Afromessage\Laravel\Validation\ValidatesAfromessageData;

class SendSmsRequest
{
    use ValidatesAfromessageData;

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
        $this->validateRequiredFields($data, ['to', 'message']);
        
        if (is_array($data['to'])) {
            $this->validatePhoneNumbers($data['to']);
        } else {
            $this->validatePhoneNumber($data['to']);
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