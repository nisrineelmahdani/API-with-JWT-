<?php
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

class AuthMiddleware {
    private $key;

    public function __construct($key) {
        $this->key = $key;
    }

    public function validateToken($headers) {
        if (!isset($headers['Authorization'])) {
            return ["error" => "Token manquant"];
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $decoded = JWT::decode($token, $this->key, ['HS256']);
            return ['id' => $decoded->id]; // Retourner l'ID de l'utilisateur
        } catch (Exception $e) {
            return ["error" => "Token invalide"];
        }
    }
}
