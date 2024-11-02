<?php
require 'vendor/autoload.php';

use \Firebase\JWT\JWT;

header("Content-Type: application/json");

$secretKey = "votre_cle_secrete";
$headers = getallheaders();

// Vérifier le token
if (!isset($headers['Authorization'])) {
    echo json_encode(["error" => "Token non fourni"]);
    exit();
}

$jwt = str_replace("Bearer ", "", $headers['Authorization']);

try {
    $decoded = JWT::decode($jwt, $secretKey, ['HS256']);
    // Accès autorisé, décoder le token JWT et extraire les données utilisateur
} catch (Exception $e) {
    echo json_encode(["error" => "Token invalide"]);
    exit();
}

// Ajoutez votre code CRUD ici, comme précédemment.
?>
