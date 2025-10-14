<?php

require_once __DIR__ . '/vendor/autoload.php';

use Afromessage\Laravel\Services\AfroMessageService;
use Afromessage\Laravel\DTO\SendSmsRequest;
use Afromessage\Laravel\DTO\BulkSmsRequest;
use Afromessage\Laravel\DTO\BulkRecipient;
use Afromessage\Laravel\DTO\SendOtpRequest;
use Afromessage\Laravel\DTO\VerifyOtpRequest;

// Load environment variables from .env file
function loadEnv() {
    $envFile = __DIR__ . '/.env';
    
    if (!file_exists($envFile)) {
        throw new Exception('.env file not found! Create one from .env.example');
    }
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Split name and value
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
                $value = $matches[1];
            }
            
            // Set environment variable
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

function main() {
    // Load environment variables from .env file
    loadEnv();
    
    $token = getenv('AFROMESSAGE_TOKEN');
    
    // Debug: Show what we loaded
    echo "🔍 Debug Info:\n";
    echo "AFROMESSAGE_TOKEN: " . ($token ? "SET (" . substr($token, 0, 10) . "...)" : "NOT SET") . "\n";
    echo "TEST_PHONE: " . (getenv('TEST_PHONE') ?: "NOT SET") . "\n";
    echo "SENDER_ID: " . (getenv('SENDER_ID') ?: "NOT SET") . "\n";
    echo "SENDER_NAME: " . (getenv('SENDER_NAME') ?: "NOT SET") . "\n";
    echo "\n";

    if (!$token) {
        throw new Exception('⚠️ Set AFROMESSAGE_TOKEN in your .env file or environment variables!');
    }

    $sdk = new AfroMessageService($token);
    
    $testPhone = getenv('TEST_PHONE');
    $senderId = getenv('SENDER_ID');
    $senderName = getenv('SENDER_NAME');
    
    echo "Testing AfroMessage Laravel SDK with REAL messages...\n";
    
    try {
        // 1. Test single SMS
        echo "\n1. Testing single SMS...\n";
        $smsRequest = new SendSmsRequest([
            'to' => $testPhone,
            'message' => "Hello from AfroMessage Laravel SDK! (Real test)",
            'from' => $senderId,
            'sender' => $senderName
        ]);
        echo "Sending SMS to: " . json_encode($smsRequest->toArray()) . "\n";
        $single = $sdk->sms()->send($smsRequest);
        echo "✅ Single SMS Response: " . json_encode($single, JSON_PRETTY_PRINT) . "\n";
        
        // 2. Test bulk SMS (uniform message)
        echo "\n2. Testing bulk SMS (uniform message)...\n";
        $bulkRequest = new BulkSmsRequest([
            'to' => [$testPhone, "+251xxxxxxxx"], // Use your second test number
            'message' => "Bulk SMS test via Laravel SDK (Real test) - Hello everyone!",
            'from' => $senderId,
            'sender' => $senderName,
            'campaign' => "Laravel SDK Test"
        ]);
        $bulk = $sdk->sms()->bulkSend($bulkRequest);
        echo "✅ Bulk SMS Response: " . json_encode($bulk, JSON_PRETTY_PRINT) . "\n";
        
        // 2b. Test bulk SMS (personalized messages)
        echo "\n2b. Testing bulk SMS (personalized messages)...\n";
        $personalizedRequest = new BulkSmsRequest([
            'to' => [
                new BulkRecipient([
                    'to' => $testPhone, 
                    'message' => "Personalized: Hello from Laravel SDK! This message is just for you."
                ]),
                new BulkRecipient([
                    'to' => "+251xxxxxxxx", 
                    'message' => "Personalized: Hello from Laravel SDK! This is a different message for you."
                ])
            ],
            'from' => $senderId,
            'sender' => $senderName,
            'campaign' => "Personalized Test"
        ]);
        echo "Sending personalized bulk SMS...\n";
        $personalizedBulk = $sdk->sms()->bulkSend($personalizedRequest);
        echo "✅ Personalized Bulk SMS Response: " . json_encode($personalizedBulk, JSON_PRETTY_PRINT) . "\n";
        
        // 3. Test OTP Challenge
        echo "\n3. Testing OTP Challenge...\n";
        $otpRequest = new SendOtpRequest([
            'to' => $testPhone,
            'pr' => "Your code",
            'from' => $senderId,
            'sender' => $senderName
        ]);
        $otp = $sdk->otp()->send($otpRequest);
        echo "✅ OTP Challenge Response: " . json_encode($otp, JSON_PRETTY_PRINT) . "\n";
        
        // 4. Test OTP Verify
        echo "\n4. Testing OTP Verify...\n";
        echo "Check your phone for the OTP code, then enter it below.\n";
        echo "Enter the OTP you received: ";
        $testCode = trim(fgets(STDIN));
        
        $verifyRequest = new VerifyOtpRequest(['to' => $testPhone, 'code' => $testCode]);
        $verify = $sdk->otp()->verify($verifyRequest);
        echo "✅ OTP Verify Response: " . json_encode($verify, JSON_PRETTY_PRINT) . "\n";
        
    } catch (Exception $error) {
        echo '❌ Error: ' . $error->getMessage() . "\n";
        if ($error->getPrevious()) {
            echo 'Previous: ' . $error->getPrevious()->getMessage() . "\n";
        }
    }
}

// Run the main function
try {
    main();
} catch (Exception $e) {
    echo "❌ Failed to run test: " . $e->getMessage() . "\n";
    echo "\n💡 Make sure you have a .env file with these variables:\n";
    echo "   AFROMESSAGE_TOKEN=your_api_token_here\n";
    echo "   TEST_PHONE=+251911500681\n";
    echo "   SENDER_ID=TEST\n";
    echo "   SENDER_NAME=TestSender\n";
    echo "\nYour .env file should be in: " . __DIR__ . "/.env\n";
    
    // Check if .env file exists
    if (file_exists(__DIR__ . '/.env')) {
        echo "✅ .env file exists\n";
        echo "Contents preview:\n";
        $envContent = file_get_contents(__DIR__ . '/.env');
        $lines = explode("\n", $envContent);
        foreach ($lines as $line) {
            if (strpos($line, 'AFROMESSAGE_TOKEN') !== false) {
                echo "AFROMESSAGE_TOKEN line: " . $line . "\n";
            }
        }
    } else {
        echo "❌ .env file NOT found in: " . __DIR__ . "/.env\n";
        echo "Create one from .env.example\n";
    }
    
    exit(1);
}