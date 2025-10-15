<?php

namespace Afromessage\Laravel\Tests\Feature;

use Afromessage\Laravel\Tests\TestCase;
use Afromessage\Laravel\DTO\SendSmsRequest;
use Afromessage\Laravel\DTO\BulkSmsRequest;
use Afromessage\Laravel\DTO\BulkRecipient;
use Afromessage\Laravel\Exceptions\ValidationException;
use Afromessage\Laravel\Services\AfroMessageService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SmsTest extends TestCase
{
    private function createMockClient(array $responses): Client
    {
        $mock = new MockHandler(array_map(
            fn($data) => new Response(200, [], json_encode($data)),
            $responses
        ));

        $handlerStack = HandlerStack::create($mock);
        
        return new Client(['handler' => $handlerStack]);
    }

    public function test_send_sms()
    {
        $mockResponse = [
            'acknowledge' => 'success',
            'response' => [
                'code' => '202',
                'message' => 'SMS is queued for delivery'
            ]
        ];

        $service = new AfroMessageService('test-token');
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($service, $this->createMockClient([$mockResponse]));

        $request = new SendSmsRequest([
            'to' => '+251xxxxxxxxxx',
            'message' => 'Test message'
        ]);

        $response = $service->sms()->send($request);
        
        $this->assertEquals('success', $response['acknowledge']);
        $this->assertEquals('202', $response['response']['code']);
    }

    public function test_bulk_sms()
    {
        $mockResponse = [
            'acknowledge' => 'success',
            'response' => [
                'code' => '202',
                'message' => 'Bulk SMS is queued for delivery'
            ]
        ];

        $service = new AfroMessageService('test-token');
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($service, $this->createMockClient([$mockResponse]));

        $request = new BulkSmsRequest([
            'to' => ['+251xxxxxxxxxx', '+251xxxxxxxxxx'],
            'message' => 'Bulk test message'
        ]);

        $response = $service->sms()->bulkSend($request);
        
        $this->assertEquals('success', $response['acknowledge']);
    }

    public function test_invalid_phone_validation()
    {
        $this->expectException(ValidationException::class);
        
        new SendSmsRequest([
            'to' => 'invalid-phone',
            'message' => 'Test message'
        ]);
    }
}