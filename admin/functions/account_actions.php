<?php
// Start the session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_login']) || !isset($_SESSION['access_level']) || $_SESSION['access_level'] != 1) {
    // Not authorized
    header("Location: index.php");
    exit;
}

// Handle different account actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Database connection (you'll need to replace these with your actual database credentials)
    $host = 'localhost';
    $dbname = 'nova_db';
    $dbuser = 'root';
    $dbpass = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Determine the action
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add':
                // Add new account
                $login = $_POST['login'] ?? '';
                $password = $_POST['password'] ?? '';
                $access_level = (int)($_POST['access_level'] ?? 0);
                $charslot = (int)($_POST['charslot'] ?? 6);
                
                // Validate inputs
                if (empty($login) || empty($password)) {
                    $_SESSION['error_message'] = "Username and password are required.";
                    header("Location: admin.php");
                    exit;
                }
                
                // Check if username already exists
                $checkStmt = $pdo->prepare("SELECT login FROM accounts WHERE login = ?");
                $checkStmt->execute([$login]);
                if ($checkStmt->rowCount() > 0) {
                    $_SESSION['error_message'] = "Username already exists.";
                    header("Location: admin.php");
                    exit;
                }
                
                // Insert new account
                $insertStmt = $pdo->prepare("
                    INSERT INTO accounts (login, password, access_level, charslot, ip, host) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([
                    $login,
                    $password,
                    $access_level,
                    $charslot,
                    $_SERVER['REMOTE_ADDR'],
                    gethostbyaddr($_SERVER['REMOTE_ADDR'])
                ]);
                
                $_SESSION['success_message'] = "Account created successfully.";
                header("Location: admin.php");
                exit;
                break;
                
            case 'edit':
                // Edit existing account
                $login = $_POST['login'] ?? '';
                $access_level = (int)($_POST['access_level'] ?? 0);
                $charslot = (int)($_POST['charslot'] ?? 6);
                
                // Check if username exists
                $checkStmt = $pdo->prepare("SELECT login FROM accounts WHERE login = ?");
                $checkStmt->execute([$login]);
                if ($checkStmt->rowCount() === 0) {
                    $_SESSION['error_message'] = "Account not found.";
                    header("Location: admin.php");
                    exit;
                }
                
                // Update query parts
                $updateFields = [];
                $params = [];
                
                // Check if password should be updated
                if (!empty($_POST['password'])) {
                    $updateFields[] = "password = ?";
                    $params[] = $_POST['password'];
                }
                
                // Add other fields to update
                $updateFields[] = "access_level = ?";
                $params[] = $access_level;
                
                $updateFields[] = "charslot = ?";
                $params[] = $charslot;
                
                // Add the login parameter at the end for the WHERE clause
                $params[] = $login;
                
                // Build and execute the update query
                $updateQuery = "UPDATE accounts SET " . implode(", ", $updateFields) . " WHERE login = ?";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute($params);
                
                $_SESSION['success_message'] = "Account updated successfully.";
                header("Location: admin.php");
                exit;
                break;
                
            case 'delete':
                // Delete account
                $login = $_POST['login'] ?? '';
                
                if (empty($login)) {
                    $_SESSION['error_message'] = "Username is required.";
                    header("Location: admin.php");
                    exit;
                }
                
                // Prevent deleting your own account
                if ($login === $_SESSION['user_login']) {
                    $_SESSION['error_message'] = "You cannot delete your own account.";
                    header("Location: admin.php");
                    exit;
                }
                
                // Delete the account
                $deleteStmt = $pdo->prepare("DELETE FROM accounts WHERE login = ?");
                $deleteStmt->execute([$login]);
                
                if ($deleteStmt->rowCount() === 0) {
                    $_SESSION['error_message'] = "Account not found or couldn't be deleted.";
                } else {
                    $_SESSION['success_message'] = "Account deleted successfully.";
                }
                
                header("Location: admin.php");
                exit;
                break;
                
            default:
                // Unknown action
                $_SESSION['error_message'] = "Unknown action requested.";
                header("Location: admin.php");
                exit;
        }
        
    } catch (PDOException $e) {
        // Database error
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: admin.php");
        exit;
    }
    
} else {
    // If not a POST request, redirect to the admin page
    header("Location: admin.php");
    exit;
}
?>