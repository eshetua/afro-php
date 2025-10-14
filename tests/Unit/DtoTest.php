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
            'to' => '+251911500681',
            'message' => 'Test message',
            'callback' => 'https://example.com/callback',
            'from' => 'TEST',
            'sender' => 'TestSender',
            'template' => 1
        ]);

        $this->assertEquals('+251911500681', $request->to);
        $this->assertEquals('Test message', $request->message);
        $this->assertEquals('https://example.com/callback', $request->callback);
        
        $array = $request->toArray();
        $this->assertArrayHasKey('to', $array);
        $this->assertArrayHasKey('message', $array);
    }

    public function test_send_sms_request_with_array_recipients()
    {
        $request = new SendSmsRequest([
            'to' => ['+251911500681', '+251922000000'],
            'message' => 'Test message'
        ]);

        $this->assertIsArray($request->to);
        $this->assertCount(2, $request->to);
    }

    public function test_send_sms_request_validation_empty_message()
    {
        $this->expectException(ValidationException::class);
        
        new SendSmsRequest([
            'to' => '+251911500681',
            'message' => ''
        ]);
    }

    // Bulk SMS DTO Tests
    public function test_bulk_sms_request_creation()
    {
        $request = new BulkSmsRequest([
            'to' => ['+251911500681', '+251922000000'],
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
                'to' => '+251911500681',
                'message' => 'Personalized message 1'
            ]),
            new BulkRecipient([
                'to' => '+251922000000', 
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
            'to' => ['+251911500681'], // Only one recipient
            'message' => 'Test message'
        ]);
    }

    public function test_bulk_recipient_creation()
    {
        $recipient = new BulkRecipient([
            'to' => '+251911500681',
            'message' => 'Personalized message'
        ]);

        $this->assertEquals('+251911500681', $recipient->to);
        $this->assertEquals('Personalized message', $recipient->message);
        
        $array = $recipient->toArray();
        $this->assertEquals('+251911500681', $array['to']);
        $this->assertEquals('Personalized message', $array['message']);
    }

    // OTP DTO Tests
    public function test_send_otp_request_creation()
    {
        $request = new SendOtpRequest([
            'to' => '+251911500681',
            'pr' => 'Your code is',
            'ps' => 'Prefix',
            'ttl' => 300,
            'len' => 6,
            't' => 'template1'
        ]);

        $this->assertEquals('+251911500681', $request->to);
        $this->assertEquals('Your code is', $request->pr);
        $this->assertEquals(300, $request->ttl);
        $this->assertEquals(6, $request->len);
        
        $array = $request->toArray();
        $this->assertArrayHasKey('to', $array);
        $this->assertArrayHasKey('pr', $array);
    }

    public function test_send_otp_request_with_custom_length()
    {
        $request = new SendOtpRequest([
            'to' => '+251911500681',
            'len' => 12
        ]);

        $this->assertEquals(12, $request->len);
    }

    public function test_send_otp_request_validation_minimum_length()
    {
        $this->expectException(ValidationException::class);
        
        new SendOtpRequest([
            'to' => '+251911500681',
            'len' => 0
        ]);
    }

    public function test_verify_otp_request_creation()
    {
        $request = new VerifyOtpRequest([
            'to' => '+251911500681',
            'code' => '123456'
        ]);

        $this->assertEquals('+251911500681', $request->to);
        $this->assertEquals('123456', $request->code);
        
        $array = $request->toArray();
        $this->assertEquals('+251911500681', $array['to']);
        $this->assertEquals('123456', $array['code']);
    }

    // OTP Validation Tests
    public function test_verify_otp_request_with_alphanumeric_code()
    {
        $request = new VerifyOtpRequest([
            'to' => '+251911500681',
            'code' => 'AB12CD'
        ]);

        $this->assertEquals('AB12CD', $request->code);
        $this->assertEquals('+251911500681', $request->to);
    }

    public function test_verify_otp_request_with_long_code()
    {
        $request = new VerifyOtpRequest([
            'to' => '+251911500681',
            'code' => 'VeryLongOTPCode2024'
        ]);

        $this->assertEquals('VeryLongOTPCode2024', $request->code);
    }

    public function test_verify_otp_request_with_special_characters()
    {
        $request = new VerifyOtpRequest([
            'to' => '+251911500681',
            'code' => 'OTP!@#2024'
        ]);

        $this->assertEquals('OTP!@#2024', $request->code);
    }

    public function test_verify_otp_request_with_mixed_case()
    {
        $request = new VerifyOtpRequest([
            'to' => '+251911500681',
            'code' => 'AbC123XyZ'
        ]);

        $this->assertEquals('AbC123XyZ', $request->code);
    }

    public function test_verify_otp_request_empty_code_validation()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Code is required');
        
        new VerifyOtpRequest([
            'to' => '+251911500681',
            'code' => ''
        ]);
    }

    public function test_verify_otp_request_whitespace_only_code_validation()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('OTP code cannot be empty');
        
        new VerifyOtpRequest([
            'to' => '+251911500681',
            'code' => '   '
        ]);
    }

    // Phone Number Validation Tests
    public function test_valid_phone_formats()
    {
        $validPhones = [
            '+251911500681',
            '251911500681'
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
        $testCases = [
            [
                'phone' => 'invalid-phone',
                'expectedError' => 'Phone number must be in E.164 format or valid digits'
            ],
            [
                'phone' => '+251-911-500-681', 
                'expectedError' => 'Phone number must be in E.164 format or valid digits'
            ],
            [
                'phone' => 'abc123',
                'expectedError' => 'Phone number must be in E.164 format or valid digits'
            ],
            [
                'phone' => '',
                'expectedError' => 'To is required'
            ]
        ];

        foreach ($testCases as $testCase) {
            try {
                new SendSmsRequest([
                    'to' => $testCase['phone'],
                    'message' => 'Test message'
                ]);
                $this->fail("Expected ValidationException for phone: {$testCase['phone']}");
            } catch (ValidationException $e) {
                $this->assertStringContainsString($testCase['expectedError'], $e->getMessage());
            }
        }
    }

    // Additional OTP tests for SendOtpRequest
    public function test_send_otp_request_with_minimum_length()
    {
        $request = new SendOtpRequest([
            'to' => '+251911500681',
            'len' => 4
        ]);

        $this->assertEquals(4, $request->len);
    }

    public function test_send_otp_request_ttl_validation()
    {
        $this->expectException(ValidationException::class);
        
        new SendOtpRequest([
            'to' => '+251911500681',
            'ttl' => 50
        ]);
    }

    public function test_send_otp_request_with_maximum_ttl()
    {
        $request = new SendOtpRequest([
            'to' => '+251911500681',
            'ttl' => 3600
        ]);

        $this->assertEquals(3600, $request->ttl);
    }
}