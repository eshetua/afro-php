<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;
use Afromessage\Laravel\Validation\ValidatesAfromessageData;

class BulkRecipient
{
    use ValidatesAfromessageData;

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
        $this->validateRequiredFields($data, ['to', 'message']);
        $this->validatePhoneNumber($data['to']);
    }

    public function toArray(): array
    {
        return [
            'to' => $this->to,
            'message' => $this->message,
        ];
    }
}