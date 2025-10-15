<?php

namespace Afromessage\Laravel\Services;

use Afromessage\Laravel\Contracts\SmsServiceInterface;
use Afromessage\Laravel\DTO\SendSmsRequest;
use Afromessage\Laravel\DTO\BulkSmsRequest;
use Afromessage\Laravel\Exceptions\AfroMessageException;

class SmsService implements SmsServiceInterface
{
    public function __construct(private AfroMessageService $client) {}

    public function send(SendSmsRequest $request): array
    {
        try {
            $payload = array_merge($this->client->getDefaultSender(), $request->toArray());
            
            $response = $this->client->getHttpClient()->post('send', [
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new AfroMessageException('SMS send failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function sendGet(SendSmsRequest $request): array
    {
        try {
            $payload = array_merge($this->client->getDefaultSender(), $request->toArray());
            
            $response = $this->client->getHttpClient()->get('send', [
                'query' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new AfroMessageException('SMS send via GET failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function bulkSend(BulkSmsRequest $request): array
    {
        try {
            $payload = array_merge($this->client->getDefaultSender(), $request->toArray());
            
            $response = $this->client->getHttpClient()->post('bulk_send', [
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new AfroMessageException('Bulk SMS send failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}