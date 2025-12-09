<?php
/**
 * JSON Web Token Implementation
 */

declare(strict_types=1);

namespace App\Core;

class JWT
{
    private static string $secret;
    private static string $algorithm;
    private static int $ttl;

    /**
     * Initialize JWT settings
     */
    private static function init(): void
    {
        self::$secret = config('jwt.secret', 'default-secret-change-me');
        self::$algorithm = config('jwt.algorithm', 'HS256');
        self::$ttl = (int) config('jwt.ttl', 3600);
    }

    /**
     * Encode payload to JWT
     */
    public static function encode(array $payload, ?int $ttl = null): string
    {
        self::init();

        $ttl = $ttl ?? self::$ttl;

        // Add standard claims
        $payload['iat'] = time();
        $payload['exp'] = time() + $ttl;
        $payload['jti'] = bin2hex(random_bytes(16));

        // Create header
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm,
        ];

        // Encode parts
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        // Create signature
        $signature = self::sign("$headerEncoded.$payloadEncoded");
        $signatureEncoded = self::base64UrlEncode($signature);

        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    /**
     * Decode and verify JWT
     */
    public static function decode(string $token): ?array
    {
        self::init();

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verify signature
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = self::sign("$headerEncoded.$payloadEncoded");

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        if (!$payload) {
            return null;
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        // Check not before
        if (isset($payload['nbf']) && $payload['nbf'] > time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Validate token without decoding
     */
    public static function validate(string $token): bool
    {
        return self::decode($token) !== null;
    }

    /**
     * Get token payload without validation
     */
    public static function getPayload(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        return json_decode(self::base64UrlDecode($parts[1]), true);
    }

    /**
     * Refresh token with new expiration
     */
    public static function refresh(string $token, ?int $ttl = null): ?string
    {
        $payload = self::decode($token);
        if (!$payload) {
            return null;
        }

        // Remove standard claims (will be regenerated)
        unset($payload['iat'], $payload['exp'], $payload['jti']);

        return self::encode($payload, $ttl);
    }

    /**
     * Create signature
     */
    private static function sign(string $data): string
    {
        return match (self::$algorithm) {
            'HS256' => hash_hmac('sha256', $data, self::$secret, true),
            'HS384' => hash_hmac('sha384', $data, self::$secret, true),
            'HS512' => hash_hmac('sha512', $data, self::$secret, true),
            default => throw new \RuntimeException('Unsupported algorithm: ' . self::$algorithm),
        };
    }

    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Generate a Personal Access Token
     */
    public static function generatePAT(int $userId, string $name, array $abilities = ['*'], ?int $expiresInDays = 365): array
    {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $expiresAt = $expiresInDays ? date('Y-m-d H:i:s', strtotime("+$expiresInDays days")) : null;

        $id = Database::insert('personal_access_tokens', [
            'user_id' => $userId,
            'name' => $name,
            'token_hash' => $hash,
            'abilities' => json_encode($abilities),
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'id' => $id,
            'token' => $token,
            'name' => $name,
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Validate Personal Access Token
     */
    public static function validatePAT(string $token): ?array
    {
        $hash = hash('sha256', $token);

        $pat = Database::selectOne(
            "SELECT pat.*, u.id as user_id, u.email, u.first_name, u.last_name, u.is_active
             FROM personal_access_tokens pat
             JOIN users u ON pat.user_id = u.id
             WHERE pat.token_hash = ? AND u.is_active = 1
             AND (pat.expires_at IS NULL OR pat.expires_at > NOW())",
            [$hash]
        );

        if (!$pat) {
            return null;
        }

        // Update last used
        Database::update('personal_access_tokens', [
            'last_used_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$pat['id']]);

        return [
            'user_id' => $pat['user_id'],
            'email' => $pat['email'],
            'first_name' => $pat['first_name'],
            'last_name' => $pat['last_name'],
            'abilities' => json_decode($pat['abilities'], true),
        ];
    }

    /**
     * Revoke Personal Access Token
     */
    public static function revokePAT(int $tokenId, int $userId): bool
    {
        return Database::delete('personal_access_tokens', 'id = ? AND user_id = ?', [$tokenId, $userId]) > 0;
    }

    /**
     * Revoke all user tokens
     */
    public static function revokeAllUserTokens(int $userId): int
    {
        return Database::delete('personal_access_tokens', 'user_id = ?', [$userId]);
    }
}
