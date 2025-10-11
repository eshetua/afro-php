<?php

namespace Afromessage\Laravel\Tests\Unit;

use Afromessage\Laravel\Tests\TestCase;
use Afromessage\Laravel\DTO\SendSmsRequest;
use Afromessage\Laravel\DTO\BulkSmsRequest;
use Afromessage\Laravel\DTO\BulkRecipient;
use Afromessage\Laravel\DTO\SendOtpRequest;
use Afromessage\Laravel\DTO\VerifyOtpRequest;
use Afromessage\Laravel\Exceptions\ValidationException;

class DtoTest extends TestCase
{
    // SMS DTO Tests
    public function test_send_sms_request_creation()
    {
        $request = new SendSmsRequest([
            'to' => '+251911xxxxxxx',
            'message' => 'Test message',
            'callback' => 'https://example.com/callback',
            'from' => 'TEST',
            'sender' => 'TestSender',
            'template' => 1
        ]);

        $this->assertEquals('+251911xxxxxxx', $request->to);
        $this->assertEquals('Test message', $request->message);
        $this->assertEquals('https://example.com/callback', $request->callback);
        
        $array = $request->toArray();
        $this->assertArrayHasKey('to', $array);
        $this->assertArrayHasKey('message', $array);
    }

    public function test_send_sms_request_with_array_recipients()
    {
        $request = new SendSmsRequest([
            'to' => ['+251911xxxxxxx', '+251911xxxxxxx'],
            'message' => 'Test message'
        ]);

        $this->assertIsArray($request->to);
        $this->assertCount(2, $request->to);
    }

    public function test_send_sms_request_validation_empty_message()
    {
        $this->expectException(ValidationException::class);
        
        new SendSmsRequest([
            'to' => '+251911xxxxxxx',
            'message' => ''
        ]);
    }

    public function test_send_sms_request_validation_long_message()
    {
        $this->expectException(ValidationException::class);
        
        new SendSmsRequest([
            'to' => '+251911xxxxxxx',
            'message' => str_repeat('a', 1601) // Exceeds 1600 characters
        ]);
    }

    // Bulk SMS DTO Tests
    public function test_bulk_sms_request_creation()
    {
        $request = new BulkSmsRequest([
            'to' => ['+251911xxxxxxx', '+251911xxxxxxx'],
            'message' => 'Bulk test message',
            'campaign' => 'TestCampaign'
        ]);

        $this->assertCount(2, $request->to);
        $this->assertEquals('Bulk test message', $request->message);
        
        $array = $request->toArray();
        $this->assertArrayHasKey('to', $array);
        $this->assertArrayHasKey('message', $array);
    }

    public function test_bulk_sms_request_with_recipient_objects()
    {
        $recipients = [
            new BulkRecipient([
                'to' => '+251911xxxxxxx',
                'message' => 'Personalized message 1'
            ]),
            new BulkRecipient([
                'to' => '+251911xxxxxxx', 
                'message' => 'Personalized message 2'
            ])
        ];

        $request = new BulkSmsRequest([
            'to' => $recipients,
            'campaign' => 'PersonalizedCampaign'
        ]);

        $this->assertCount(2, $request->to);
        $this->assertInstanceOf(BulkRecipient::class, $request->to[0]);
        
        $array = $request->toArray();
        $this->assertArrayHasKey('to', $array);
        $this->assertIsArray($array['to']);
    }

    public function test_bulk_sms_request_validation_insufficient_recipients()
    {
        $this->expectException(ValidationException::class);
        
        new BulkSmsRequest([
            'to' => ['+251911xxxxxxx'], // Only one recipient
            'message' => 'Test message'
        ]);
    }

    public function test_bulk_recipient_creation()
    {
        $recipient = new BulkRecipient([
            'to' => '+251911xxxxxxx',
            'message' => 'Personalized message'
        ]);

        $this->assertEquals('+251911xxxxxxx', $recipient->to);
        $this->assertEquals('Personalized message', $recipient->message);
        
        $array = $recipient->toArray();
        $this->assertEquals('+251911xxxxxxx', $array['to']);
        $this->assertEquals('Personalized message', $array['message']);
    }

    // OTP DTO Tests
    public function test_send_otp_request_creation()
    {
        $request = new SendOtpRequest([
            'to' => '+251911xxxxxxx',
            'pr' => 'Your code is',
            'ps' => 'Prefix',
            'ttl' => 300,
            'len' => 6,
            't' => 'template1'
        ]);

        $this->assertEquals('+251911xxxxxxx', $request->to);
        $this->assertEquals('Your code is', $request->pr);
        $this->assertEquals(300, $request->ttl);
        $this->assertEquals(6, $request->len);
        
        $array = $request->toArray();
        $this->assertArrayHasKey('to', $array);
        $this->assertArrayHasKey('pr', $array);
    }

    public function test_verify_otp_request_creation()
    {
        $request = new VerifyOtpRequest([
            'to' => '+251911xxxxxxx',
            'code' => '123456'
        ]);

        $this->assertEquals('+251911xxxxxxx', $request->to);
        $this->assertEquals('123456', $request->code);
        
        $array = $request->toArray();
        $this->assertEquals('+251911xxxxxxx', $array['to']);
        $this->assertEquals('123456', $array['code']);
    }

    public function test_verify_otp_request_validation_short_code()
    {
        $this->expectException(ValidationException::class);
        
        new VerifyOtpRequest([
            'to' => '+251911xxxxxxx',
            'code' => '123' // Too short
        ]);
    }

    public function test_verify_otp_request_validation_long_code()
    {
        $this->expectException(ValidationException::class);
        
        new VerifyOtpRequest([
            'to' => '+251911xxxxxxx',
            'code' => '123456789' // Too long
        ]);
    }

    public function test_verify_otp_request_validation_non_numeric_code()
    {
        $this->expectException(ValidationException::class);
        
        new VerifyOtpRequest([
            'to' => '+251911xxxxxxx',
            'code' => '12a456' // Contains non-digit
        ]);
    }

    // Phone Number Validation Tests
    public function test_valid_phone_formats()
    {
        $validPhones = [
            '+251911xxxxxxx',
            '+251911xxxxxxx'
        ];

        foreach ($validPhones as $phone) {
            $request = new SendSmsRequest([
                'to' => $phone,
                'message' => 'Test message'
            ]);
            
            $this->assertEquals($phone, $request->to);
        }
    }

    public function test_invalid_phone_formats()
{
    $invalidPhones = [
        'invalid-phone',
        '+251-911-500-681',
        'abc123',
        ''
    ];

    foreach ($invalidPhones as $phone) {
        try {
            new SendSmsRequest([
                'to' => $phone,
                'message' => 'Test message'
            ]);
            $this->fail("Expected ValidationException for phone: $phone");
        } catch (ValidationException $e) {
            if ($phone === '') {
                $this->assertStringContainsString('Recipient phone number is required', $e->getMessage());
            } else {
                $this->assertStringContainsString('Phone number', $e->getMessage());
            }
        }
    }
}
}

