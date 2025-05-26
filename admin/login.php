<?php
// Start the session
session_start();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get login credentials from the form
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($login) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both username and password.";
        header("Location: index.php");
        exit;
    }
    
    // Database connection (you'll need to replace these with your actual database credentials)
    $host = 'localhost';
    $dbname = 'nova_db';
    $dbuser = 'root';
    $dbpass = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if the user exists and the password is correct
        $stmt = $pdo->prepare("SELECT login, password, access_level FROM accounts WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['password'] === $password) {
            // Authentication successful
            
            // Store user information in the session
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['access_level'] = $user['access_level'];
            
            // Update the lastactive timestamp
            $updateStmt = $pdo->prepare("UPDATE accounts SET lastactive = NOW(), ip = ? WHERE login = ?");
            $updateStmt->execute([$_SERVER['REMOTE_ADDR'], $login]);
            
            // Redirect based on access level
            if ($user['access_level'] == 1) {
                // Admin user
                header("Location: admin.php");
            } else {
                // Regular user
                header("Location: index.php");
            }
            exit;
        } else {
            // Authentication failed
            $_SESSION['login_error'] = "Invalid username or password.";
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        // Database error
        $_SESSION['login_error'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
} else {
    // If not a POST request, redirect to the homepage
    header("Location: index.php");
    exit;
}
?>