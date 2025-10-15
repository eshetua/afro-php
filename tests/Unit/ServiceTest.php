<?php

namespace Afromessage\Laravel\Tests\Unit;

use Afromessage\Laravel\Tests\TestCase;
use Afromessage\Laravel\Services\AfroMessageService;
use Afromessage\Laravel\Exceptions\AfroMessageException;

class ServiceTest extends TestCase
{
    public function test_service_initialization()
    {
        $service = app(\Afromessage\Laravel\Contracts\AfroMessageInterface::class);
        
        $this->assertInstanceOf(AfroMessageService::class, $service);
        $this->assertNotNull($service->sms());
        $this->assertNotNull($service->otp());
    }

    public function test_direct_service_initialization()
    {
        $service = new AfroMessageService('test-token');
        
        $this->assertInstanceOf(AfroMessageService::class, $service);
        $this->assertNotNull($service->sms());
        $this->assertNotNull($service->otp());
    }

    public function test_service_requires_token()
    {
        $this->expectException(AfroMessageException::class);
        $this->expectExceptionMessage('AfroMessage token is required');
        
        new AfroMessageService('');
    }
}