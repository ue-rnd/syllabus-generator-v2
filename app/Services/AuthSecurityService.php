<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthSecurityService
{
    /**
     * Handle successful login
     */
    public function handleSuccessfulLogin(User $user, string $ip): void
    {
        $user->updateLastLogin($ip);

        Log::info('User login successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $ip,
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle failed login attempt
     */
    public function handleFailedLogin(string $email, string $ip): void
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->incrementLoginAttempts();

            Log::warning('Failed login attempt', [
                'user_id' => $user->id,
                'email' => $email,
                'ip' => $ip,
                'attempts' => $user->login_attempts,
                'timestamp' => now(),
            ]);
        } else {
            Log::warning('Failed login attempt for non-existent user', [
                'email' => $email,
                'ip' => $ip,
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * Check if password meets security requirements
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }

        return $errors;
    }

    /**
     * Check if password has been used recently
     */
    public function isPasswordRecentlyUsed(User $user, string $password): bool
    {
        // Check against current password
        if (Hash::check($password, $user->password)) {
            return true;
        }

        // You could extend this to check against a password history table
        return false;
    }

    /**
     * Force password change for user
     */
    public function forcePasswordChange(User $user): void
    {
        $user->update([
            'must_change_password' => true,
        ]);

        Log::info('Password change forced for user', [
            'user_id' => $user->id,
            'email' => $user->email,
            'timestamp' => now(),
        ]);
    }

    /**
     * Lock user account
     */
    public function lockAccount(User $user, int $minutes = 30): void
    {
        $user->lockAccount(now()->addMinutes($minutes));

        Log::warning('User account locked due to multiple failed attempts', [
            'user_id' => $user->id,
            'email' => $user->email,
            'locked_until' => $user->locked_until,
            'timestamp' => now(),
        ]);
    }

    /**
     * Unlock user account
     */
    public function unlockAccount(User $user): void
    {
        $user->unlockAccount();

        Log::info('User account unlocked', [
            'user_id' => $user->id,
            'email' => $user->email,
            'timestamp' => now(),
        ]);
    }

    /**
     * Generate secure random password
     */
    public function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';

        // Ensure at least one character from each category
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Fill the rest randomly
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Generate two-factor authentication secret
     */
    public function generateTwoFactorSecret(): string
    {
        return base32_encode(random_bytes(32));
    }

    /**
     * Generate recovery codes for two-factor authentication
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(md5(random_bytes(32)), 0, 8));
        }

        return $codes;
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, User $user, array $context = []): void
    {
        Log::channel('security')->info($event, array_merge([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'timestamp' => now(),
        ], $context));
    }
}

/**
 * Base32 encoding function (simple implementation)
 */
function base32_encode(string $data): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $output = '';
    $v = 0;
    $vbits = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $v = ($v << 8) | ord($data[$i]);
        $vbits += 8;

        while ($vbits >= 5) {
            $output .= $alphabet[($v >> ($vbits - 5)) & 31];
            $vbits -= 5;
        }
    }

    if ($vbits > 0) {
        $output .= $alphabet[($v << (5 - $vbits)) & 31];
    }

    return $output;
}