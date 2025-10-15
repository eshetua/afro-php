<?php

namespace Afromessage\Laravel\Validation;

use Afromessage\Laravel\Exceptions\ValidationException;

trait ValidatesAfromessageData
{
    /**
     * Validate phone number
     */
    private function validatePhoneNumber(string $phone): void
    {
        $cleanedPhone = preg_replace('/\s+/', '', $phone);
        
        if (!preg_match('/^(\+\d+|\d+)$/', $cleanedPhone)) {
            throw new ValidationException('Phone number must be in E.164 format or valid digits');
        }
    }

    /**
     * Validate OTP code
     */
    private function validateOtpCode(string $code): void
    {
        $cleanedCode = preg_replace('/\s+/', '', $code);
        
        if (empty($cleanedCode)) {
            throw new ValidationException('OTP code cannot be empty');
        }

        // Minimum length requirement
        if (strlen($cleanedCode) < 4) {
            throw new ValidationException('OTP code must be at least 4 characters');
        }
    }

    /**
     * Validate multiple phone numbers
     */
    private function validatePhoneNumbers(array $phones): void
    {
        foreach ($phones as $phone) {
            $this->validatePhoneNumber($phone);
        }
    }

    /**
     * Validate required fields
     */
    private function validateRequiredFields(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new ValidationException(ucfirst($field) . ' is required');
            }
        }
    }

    /**
     * Validate array field with minimum count
     */
    private function validateArrayWithMinCount(array $data, string $field, int $minCount, string $message): void
    {
        if (empty($data[$field]) || !is_array($data[$field]) || count($data[$field]) < $minCount) {
            throw new ValidationException($message);
        }
    }

    /**
     * Validate numeric range
     */
    private function validateNumericRange($value, int $min, int $max, string $fieldName): void
    {
        if ($value < $min || $value > $max) {
            throw new ValidationException("{$fieldName} must be between {$min} and {$max}");
        }
    }
}