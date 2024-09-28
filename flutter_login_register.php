<?php
require 'db.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

class UserAuth {
    private $pdo;

    function __construct($host, $db, $user, $pass) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
            exit();
        }
    }

    // Register user
    function registerUser($username, $password, $first_name, $middle_name, $last_name, $email, $phone, $address) {
        $sql = "INSERT INTO users (username, password, first_name, middle_name, last_name, email, phone, address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        try {
            $stmt->execute([$username, $password, $first_name, $middle_name, $last_name, $email, $phone, $address]);
            echo json_encode(['message' => 'Registration successful']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    // Login user
    function loginUser($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo json_encode(['message' => 'Login successful', 'user' => $user]);
        } else {
            echo json_encode(['error' => 'Invalid username or password']);
        }
    }

    // Update user information
    function updateUser($id, $username, $password, $first_name, $middle_name, $last_name, $email, $phone, $address) {
        // If password is empty, do not update it
        if (empty($password)) {
            $sql = "UPDATE users SET username = ?, first_name = ?, middle_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $params = [$username, $first_name, $middle_name, $last_name, $email, $phone, $address, $id];
        } else {
            $sql = "UPDATE users SET username = ?, password = ?, first_name = ?, middle_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $params = [$username, $password, $first_name, $middle_name, $last_name, $email, $phone, $address, $id];
        }

        try {
            $stmt->execute($params);
            echo json_encode(['message' => 'Update successful']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }
}

// Initialize the class with database credentials
$auth = new UserAuth('localhost', 'pet_system', 'root', '');

// Handling the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($action === 'register') {
        $auth->registerUser(
            $data['username'], 
            $data['password'], 
            $data['first_name'], 
            $data['middle_name'], 
            $data['last_name'], 
            $data['email'], 
            $data['phone'], 
            $data['address']
        );
    } elseif ($action === 'login') {
        $auth->loginUser($data['username'], $data['password']);
    } elseif ($action === 'update') { // New Update Action
        // Ensure 'id' is provided
        if (!isset($data['id'])) {
            echo json_encode(['error' => 'User ID is required for update']);
            exit();
        }

        $auth->updateUser(
            $data['id'], 
            $data['username'], 
            $data['password'], // Can be empty if not updating password
            $data['first_name'], 
            $data['middle_name'], 
            $data['last_name'], 
            $data['email'], 
            $data['phone'], 
            $data['address']
        );
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
}
?>
