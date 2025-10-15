# AfroMessage Laravel SDK

A complete Laravel package for the AfroMessage SMS and OTP API.
This package makes it easy to send single SMS, bulk SMS, and handle OTP challenges with Laravel elegance.

---

## 🚀 Installation

Install the package via Composer:

```bash
Install the package via Composer:
```
# 🔧 Configuration
Publish the configuration file:
```bash
php artisan vendor:publish --tag=afromessage-config
```
Add your credentials to your .env file:
```bash
AFROMESSAGE_TOKEN=your_api_token_here
AFROMESSAGE_BASE_URL=https://api.afromessage.com/api/
SENDER_ID=your_sender_id
SENDER_NAME=your_sender_name
```
# 🔑 Quick Start
Using Facade (Recommended):
```bash
use Afromessage\Laravel\Facades\AfroMessage;
use Afromessage\Laravel\DTO\SendSmsRequest;
use Afromessage\Laravel\DTO\BulkSmsRequest;
use Afromessage\Laravel\DTO\BulkRecipient;
use Afromessage\Laravel\DTO\SendOtpRequest;
use Afromessage\Laravel\DTO\VerifyOtpRequest;

// --- Single SMS ---
$smsRequest = new SendSmsRequest([
    'to' => '+251xxxxxxxxxx',
    'message' => 'Hello from AfroMessage Laravel SDK!',
    'from' => 'TEST',
    'sender' => 'TestSender'
]);

$response = AfroMessage::sms()->send($smsRequest);

// --- Bulk SMS ---
$bulkRequest = new BulkSmsRequest([
    'to' => ['+251xxxxxxxxxx', '+251xxxxxxxxxx'],
    'message' => 'Hello, bulk users!',
    'from' => 'TEST',
    'sender' => 'TestSender',
    'campaign' => 'MyCampaign'
]);

$bulkResponse = AfroMessage::sms()->bulkSend($bulkRequest);

// --- Personalized Bulk SMS ---
$personalizedRequest = new BulkSmsRequest([
    'to' => [
        new BulkRecipient([
            'to' => '+251xxxxxxxxxx', 
            'message' => 'Hi Yonas!'
        ]),
        new BulkRecipient([
            'to' => '+251xxxxxxxxxx', 
            'message' => 'Hi Eshetu!'
        ])
    ],
    'from' => 'TEST',
    'sender' => 'TestSender',
    'campaign' => 'PersonalizedCampaign'
]);

$personalizedResponse = AfroMessage::sms()->bulkSend($personalizedRequest);

// --- OTP Challenge ---
$otpRequest = new SendOtpRequest([
    'to' => '+251xxxxxxxxxx',
    'pr' => 'Your code'
]);

$otpResponse = AfroMessage::otp()->send($otpRequest);

// --- Verify OTP ---
$verifyRequest = new VerifyOtpRequest([
    'to' => '+251xxxxxxxxxx',
    'code' => '123456'
]);

$verifyResponse = AfroMessage::otp()->verify($verifyRequest);
```
## using Dependancy injection:
``` bash
use Afromessage\Laravel\Contracts\AfroMessageInterface;

class SmsController extends Controller
{
    public function sendSms(AfroMessageInterface $afroMessage)
    {
        $request = new SendSmsRequest([
            'to' => '+251xxxxxxxxxx',
            'message' => 'Hello from controller!'
        ]);

        $response = $afroMessage->sms()->send($request);
        
        return response()->json($response);
    }
}
```
## using helper function
```bash
$response = app('afromessage')->sms()->send($smsRequest);
```
## 📦 Features

## ✅ Send single SMS
## ✅ Send bulk SMS campaigns
## ✅ send personalized bulk SMS
## ✅ Generate OTP challenges
## ✅ Verify OTP codes
## ✅ Built-in error handling
## ✅ Request/response logging for debugging


# ⚡ API Reference

## Single SMS
```bash
use Afromessage\Laravel\DTO\SendSmsRequest;

$request = new SendSmsRequest([
    'to' => '+251xxxxxxxxxx',           // Required
    'message' => 'Hello World!',       // Required
    'from' => 'TEST',                  // Optional
    'sender' => 'TestSender',          // Optional
    'callback' => 'https://example.com/callback', // Optional
    'template' => 0                    // Optional
]);

$response = AfroMessage::sms()->send($request);
```
## Bulk SMS
```bash
use Afromessage\Laravel\DTO\BulkSmsRequest;

$request = new BulkSmsRequest([
    'to' => ['+251xxxxxxxxxx', '+251xxxxxxxxxx'], // Required (min 2 numbers)
    'message' => 'Hello everyone!',             // Required
    'from' => 'TEST',                           // Optional
    'sender' => 'TestSender',                   // Optional
    'campaign' => 'MyCampaign',                 // Optional
    'create_callback' => 'https://example.com/create', // Optional
    'status_callback' => 'https://example.com/status'  // Optional
]);

$response = AfroMessage::sms()->bulkSend($request);
```
## Bulk SMS (Personalized messages)
```bash
use Afromessage\Laravel\DTO\BulkSmsRequest;
use Afromessage\Laravel\DTO\BulkRecipient;

$request = new BulkSmsRequest([
    'to' => [
        new BulkRecipient([
            'to' => '+251xxxxxxxxxx',
            'message' => 'Hi Yonas, welcome!'
        ]),
        new BulkRecipient([
            'to' => '+251xxxxxxxxxx', 
            'message' => 'Hi Eshetu, welcome!'
        ])
    ],
    'from' => 'TEST',
    'sender' => 'TestSender',
    'campaign' => 'PersonalizedWelcome'
]);

$response = AfroMessage::sms()->bulkSend($request);
```
## OTP Challenge
```bash
use Afromessage\Laravel\DTO\SendOtpRequest;

$request = new SendOtpRequest([
    'to' => '+251xxxxxxxxxx',      // Required
    'pr' => 'Your code is',       // Optional - prefix
    'ps' => 'suffix',             // Optional - suffix  
    'ttl' => 300,                 // Optional - time to live (60-3600 seconds)
    'len' => 6,                   // Optional - OTP length (flexible)
    'from' => 'TEST',             // Optional
    'sender' => 'TestSender'      // Optional
]);

$response = AfroMessage::otp()->send($request);
```
## Verify OTP
```bash
use Afromessage\Laravel\DTO\VerifyOtpRequest;

// Traditional numeric OTP
$request = new VerifyOtpRequest([
    'to' => '+251xxxxxxxxxx',
    'code' => '123456'
]);

// Alphanumeric OTP
$request = new VerifyOtpRequest([
    'to' => '+251xxxxxxxxxx',
    'code' => 'AB12CD'
]);

// Long OTP with special characters
$request = new VerifyOtpRequest([
    'to' => '+251xxxxxxxxxx',
    'code' => 'MySecureOTP!2024'
]);

$response = AfroMessage::otp()->verify($request);
```
# 🛠 Error Handling
## All exceptions extend Afromessage\Laravel\Exceptions\AfroMessageException:
```bash
use Afromessage\Laravel\Exceptions\AfroMessageException;
use Afromessage\Laravel\Exceptions\ValidationException;

try {
    $response = AfroMessage::sms()->send($request);
} catch (ValidationException $e) {
    // Input validation failed
    return response()->json(['error' => $e->getMessage()], 422);
} catch (AfroMessageException $e) {
    // API or network error
    return response()->json(['error' => $e->getMessage()], 500);
}
```
## Common Exceptions:

#    ValidationException - Input validation errors

#    AfroMessageException - API errors, network issues, authentication failures

# 🔍 Validation Rules
## Phone Numbers:
Must be in E.164 format or valid digits
## Messages:
Minimum 1 characterk
## OTP Codes:
Flexible format: Can be numeric, alphanumeric, or contain special characters.
Flexible length: No fixed limits (configurable if needed).
Required for verification
# 🧪 Testing
Run the test suite:
```bash
 vendor/bin/phpunit
```
Run specific test suites:
```bash
# Unit tests only
vendor/bin/phpunit --testsuite Unit

# Feature tests only  
vendor/bin/phpunit --testsuite Feature

# Specific test file
vendor/bin/phpunit tests/Feature/SmsTest.php
```

# 🧪 Advanced Example (Real Test)
## Create a .env file in your package root:
```bash
AFROMESSAGE_TOKEN=your_actual_token_here
TEST_PHONE=+251xxxxxxxxxx
SENDER_ID=TEST
SENDER_NAME=TestSender
```
## Run the real test script:
```bash
php real-test.php
```
## We provide a full interactive test script in [RealTest.php](https://githubrepo.com)
.
## It shows how to send SMS, bulk SMS, and run OTP challenge + verification with .env loading.
## Run it with:
```bash
php  RealTest.php
```
## ⚠️ Note: Running this will send real SMS/OTP messages and may incur costs.

# 🤝 Contributing

## Contributions are welcome! To contribute:

##    1. Fork the repo

##    2. Create your feature branch (git checkout -b feature/my-feature)

##    3. Commit your changes (git commit -m 'Add my feature')

##    4. Push to the branch (git push origin feature/my-feature)

##    5. Open a Pull Request

## Run tests before submitting:
```bash
comoser test
```

# 📜 License

## This project is licensed under the MIT License.
## See LICENSE
## for details.


# 🙋 Support
## For issues or feature requests, open a GitHub Issue






