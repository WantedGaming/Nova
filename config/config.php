<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'nova_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'NOVA Digital Solutions');
define('SITE_URL', 'http://localhost/nova');
define('ADMIN_EMAIL', 'admin@nova.com');

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Error Reporting Settings
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Timezone Settings
date_default_timezone_set('UTC');

// Database Connection Function
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>