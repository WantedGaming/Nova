<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in response
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

try {
    // Test database connection first
    if (!$pdo) {
        throw new Exception('Database connection is null');
    }
    
    // Check user credentials in accounts table
    $stmt = $pdo->prepare("SELECT login, password, access_level FROM accounts WHERE login = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        exit;
    }

    // Simple password verification (you should use proper hashing in production)
    if ($password !== $user['password']) {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
        exit;
    }

    // Set session variables
    $_SESSION['user_id'] = $user['login'];
    $_SESSION['username'] = $user['login'];
    $_SESSION['access_level'] = (int)$user['access_level'];

    echo json_encode([
        'success' => true, 
        'message' => 'Login successful',
        'redirect' => $_SESSION['access_level'] >= 1 ? '/admin/dashboard.php' : '/'
    ]);

} catch (PDOException $e) {
    error_log("Login PDO error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Login general error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>