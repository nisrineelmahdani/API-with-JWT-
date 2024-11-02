<?php
session_start(); // Démarrez la session pour accéder au token CSRF
require 'AuthMiddleware.php'; // Incluez le middleware
// Vérifiez le token CSRF pour chaque requête qui modifie l'état
$key = "PIANOTILES2HJ";
$authMiddleware = new AuthMiddleware($key);
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $headers = getallheaders();
    $csrf_token = $headers['X-CSRF-Token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) { // Vérifiez que le token correspond
        echo json_encode(["error" => "Token CSRF invalide"]);
        exit();
    }
}



// Ajoutez ceci en haut de index.php : verifier le jwt pour chaque operation crud
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

// Vérifiez si le token JWT est présent et valide
function validateJWT($jwt) {
    $key = "PIANOTILES2HJ"; // Remplacez par votre clé secrète

    try {
        $decoded = JWT::decode($jwt, $key, ['HS256']);
        return $decoded->id; // Retourner l'ID de l'utilisateur
    } catch (Exception $e) {
        return false; // Token invalide
    }
}

// Vérifiez le token JWT pour chaque requête (à placer avant le switch)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Ignore le POST pour la création  car c'est généralement lors de la connexion qu'un utilisateur reçoit un token.
    $headers = getallheaders();

         
   /* if (!isset($headers['Authorization'])) {
        echo json_encode(["error" => "Token manquant"]);
        exit();
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $userId = validateJWT($token);
    if (!$userId) {
        echo json_encode(["error" => "Token invalide"]);
        exit();
    }*/ 
    // remplacer ca par un middelwear:*
    $validationResult = $authMiddleware->validateToken($headers);

    if (isset($validationResult['error'])) {
        echo json_encode($validationResult);
        exit();
    }
    $userId = $validationResult['id']; // id est recupere apres que token est verifié

}



// pour creer une API 
header("Content-Type: application/json");

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

// Obtenir l'identifiant de la tâche, s'il est fourni
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($method) {
    case 'POST': // Créer une nouvelle tâche
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['title'])) {
            echo json_encode(["error" => "Le titre de la tâche est requis"]);
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO tasks (title, description) VALUES (:title, :description)");
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null
        ]);
        echo json_encode(["message" => "Tâche créée avec succès"]);
        break;

    case 'GET': // Lire une ou plusieurs tâches
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($task ? $task : ["error" => "Tâche non trouvée"]);
        } else {
            $stmt = $pdo->query("SELECT * FROM tasks");
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tasks);
        }
        break;

    case 'PUT': // Mettre à jour une tâche existante
        if (!$id) {
            echo json_encode(["error" => "ID de la tâche requis"]);
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['title'])) {
            echo json_encode(["error" => "Le titre de la tâche est requis"]);
            exit();
        }

        $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description WHERE id = :id");
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':id' => $id
        ]);
        echo json_encode(["message" => "Tâche mise à jour avec succès"]);
        break;

    case 'DELETE': // Supprimer une tâche
        if (!$id) {
            echo json_encode(["error" => "ID de la tâche requis"]);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(["message" => "Tâche supprimée avec succès"]);
        break;

    default:
        echo json_encode(["error" => "Méthode non autorisée"]);
        break;
}
?>
