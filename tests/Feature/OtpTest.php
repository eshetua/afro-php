<?php

namespace Afromessage\Laravel\Tests\Feature;

use Afromessage\Laravel\Tests\TestCase;
use Afromessage\Laravel\DTO\SendOtpRequest;
use Afromessage\Laravel\DTO\VerifyOtpRequest;
use Afromessage\Laravel\Exceptions\ValidationException;
use Afromessage\Laravel\Services\AfroMessageService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class OtpTest extends TestCase
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

    public function test_send_otp()
    {
        $mockResponse = [
            'acknowledge' => 'success',
            'response' => [
                'code' => '202',
                'to' => '+2519xxxxxxxx',
                'request' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890'
            ]
        ];

        $service = new AfroMessageService('test-token');
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($service, $this->createMockClient([$mockResponse]));

        $request = new SendOtpRequest([
            'to' => '+2519xxxxxxxx',
            'pr' => 'Your code'
        ]);

        $response = $service->otp()->send($request);
        
        $this->assertEquals('success', $response['acknowledge']);
        $this->assertEquals('202', $response['response']['code']);
        $this->assertEquals('+2519xxxxxxxx', $response['response']['to']);
    }

    public function test_verify_otp()
    {
        $mockResponse = [
            'acknowledge' => 'success',
            'response' => [
                'code' => '202',
                'message' => 'OTP verified successfully'
            ]
        ];

        $service = new AfroMessageService('test-token');
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($service, $this->createMockClient([$mockResponse]));

        $request = new VerifyOtpRequest([
            'to' => '+2519xxxxxxxx',
            'code' => '123456'
        ]);

        $response = $service->otp()->verify($request);
        
        $this->assertEquals('success', $response['acknowledge']);
        $this->assertEquals('202', $response['response']['code']);
    }

    public function test_invalid_phone_validation()
    {
        $this->expectException(ValidationException::class);
        
        new SendOtpRequest([
            'to' => 'invalid-phone',
            'pr' => 'Your code'
        ]);
    }

    public function test_invalid_otp_code_validation()
    {
        $this->expectException(ValidationException::class);
        
        new VerifyOtpRequest([
            'to' => '+2519xxxxxxxx',
            'code' => 'abc' // Invalid - should be numeric
        ]);
    }

    public function test_otp_length_validation()
    {
        $this->expectException(ValidationException::class);
        
        new SendOtpRequest([
            'to' => '+2519xxxxxxxx',
            'len' => 3 // Invalid - should be between 4-8
        ]);
    }

    public function test_otp_ttl_validation()
    {
        $this->expectException(ValidationException::class);
        
        new SendOtpRequest([
            'to' => '+2519xxxxxxxx',
            'ttl' => 50 // Invalid - should be between 60-3600
        ]);
    }
}