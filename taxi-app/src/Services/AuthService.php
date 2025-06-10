<?php

namespace AnnaBozzi\TaxiApp\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthService {
    private string $secretKey;
    
    public function __construct() {
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'default-secret';
    }
    
    public function generateToken(array $payload): string {
        $payload['exp'] = time() + (24 * 60 * 60);
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
    
    public function validateToken(string $token): array {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception('Invalid token');
        }
    }
}
