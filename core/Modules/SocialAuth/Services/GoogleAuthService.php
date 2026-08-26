<?php

namespace Modules\SocialAuth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class GoogleAuthService
{
    /**
     * Find an existing user by google_id or email, or create a new one.
     */
    public static function findOrCreate(SocialiteUser $googleUser): User
    {
        // 1. Match by google_id (fastest, most reliable)
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            return $user;
        }

        // 2. Match by email — link the google_id to an existing account
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update(['google_id' => $googleUser->getId()]);
            return $user;
        }

        // 3. New user — auto-register
        $username = self::generateUsername($googleUser->getName());

        return User::create([
            'name'           => $googleUser->getName(),
            'email'          => $googleUser->getEmail(),
            'username'       => $username,
            'password'       => Hash::make(Str::random(24)),
            'google_id'      => $googleUser->getId(),
            'email_verified' => 1, // Google already verified their email
        ]);
    }

    /**
     * Generate a unique username from the Google display name.
     */
    private static function generateUsername(string $name): string
    {
        $base     = 'gl_' . Str::slug($name, '_');
        $username = $base;
        $i        = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }

        return $username;
    }

    /**
     * Create a signed token for cross-domain tenant login.
     * Valid for 60 seconds.
     */
    public static function makeTenantToken(int $userId): string
    {
        $payload = $userId . '|' . (time() + 60) . '|' . Str::random(16);
        $sig     = hash_hmac('sha256', $payload, config('app.key'));
        return base64_encode($payload . '|' . $sig);
    }

    /**
     * Validate and unpack a tenant token. Returns user_id or null.
     */
    public static function validateTenantToken(string $token): ?int
    {
        try {
            $decoded = base64_decode($token);
            $parts   = explode('|', $decoded);

            if (count($parts) !== 4) {
                return null;
            }

            [$userId, $expires, $nonce, $sig] = $parts;
            $payload  = "{$userId}|{$expires}|{$nonce}";
            $expected = hash_hmac('sha256', $payload, config('app.key'));

            if (!hash_equals($expected, $sig)) {
                return null;
            }

            if (time() > (int) $expires) {
                return null;
            }

            return (int) $userId;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
