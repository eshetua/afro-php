<?php

namespace Afromessage\Laravel\DTO;

use Afromessage\Laravel\Exceptions\ValidationException;
use Afromessage\Laravel\Validation\ValidatesAfromessageData;

class BulkSmsRequest
{
    use ValidatesAfromessageData;

    public array $to;
    public ?string $message = null;
    public ?string $from = null;
    public ?string $sender = null;
    public ?string $campaign = null;
    public ?string $createCallback = null;
    public ?string $statusCallback = null;

    public function __construct(array $data)
    {
        $this->validate($data);
        
        $this->to = $data['to'];
        $this->message = $data['message'] ?? null;
        $this->from = $data['from'] ?? null;
        $this->sender = $data['sender'] ?? null;
        $this->campaign = $data['campaign'] ?? null;
        $this->createCallback = $data['create_callback'] ?? null;
        $this->statusCallback = $data['status_callback'] ?? null;
    }

    private function validate(array $data): void
    {
        $this->validateArrayWithMinCount($data, 'to', 2, 'Bulk SMS requires at least 2 recipients');

        $firstRecipient = $data['to'][0];
        
        if ($firstRecipient instanceof BulkRecipient) {
            foreach ($data['to'] as $recipient) {
                if (!$recipient instanceof BulkRecipient) {
                    throw new ValidationException('All recipients must be BulkRecipient objects for personalized messages');
                }
            }
        } else {
            if (empty($data['message'])) {
                throw new ValidationException('Message is required for uniform bulk SMS');
            }
            $this->validatePhoneNumbers($data['to']);
        }
    }

    public function toArray(): array
    {
        $data = array_filter([
            'to' => $this->to,
            'message' => $this->message,
            'from' => $this->from,
            'sender' => $this->sender,
            'campaign' => $this->campaign,
            'create_callback' => $this->createCallback,
            'status_callback' => $this->statusCallback,
        ], fn($value) => $value !== null);

        if (isset($data['to']) && is_array($data['to']) && !empty($data['to']) && $data['to'][0] instanceof BulkRecipient) {
            $data['to'] = array_map(fn($recipient) => $recipient->toArray(), $data['to']);
        }

        return $data;
    }
}