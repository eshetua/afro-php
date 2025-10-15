<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;
use Afromessage\Laravel\Validation\ValidatesAfromessageData;

class VerifyOtpRequest
{
    use ValidatesAfromessageData;

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
        $this->validateRequiredFields($data, ['to', 'code']);
        $this->validatePhoneNumber($data['to']);
        $this->validateOtpCode($data['code']);
    }

    public function toArray(): array
    {
        return [
            'to' => $this->to,
            'code' => $this->code,
        ];
    }
}