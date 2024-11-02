<?php
session_start();
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=tasks_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit();
}

// Obtenir la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // Vérifier les identifiants (à adapter selon votre table utilisateur)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) { // Assurez-vous que le mot de passe est haché
        // Créer un token JWT
        $key = "PIANOTILES2HJ"; // Remplacez par votre clé secrète
        $payload = [
            'iat' => time(), // Timestamp d'émission
            'exp' => time() + 3600, // Expiration dans une heure
            'id' => $user['id'] // Identifier l'utilisateur
        ];

        $jwt = JWT::encode($payload, $key);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Créer un token CSRF
        echo json_encode(["token" => $jwt]);
    } else {
        echo json_encode(["error" => "Identifiants incorrects"]);
    }
} else {
    echo json_encode(["error" => "Méthode non autorisée"]);
}
?>
