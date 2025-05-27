<?php
// Path configuration for the website

// Get the document root and current script path
$documentRoot = $_SERVER['DOCUMENT_ROOT'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$requestUri = $_SERVER['REQUEST_URI'];

// Calculate the base URL dynamically
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    
    // Get the directory of the main index.php (assuming it's in the root)
    $basePath = dirname($scriptName);
    
    // If we're in a subdirectory, we need to go back to the root
    $currentPath = $_SERVER['REQUEST_URI'];
    $depth = 0;
    
    if (strpos($currentPath, '/admin/') !== false) {
        $depth = substr_count(trim(str_replace('/admin/', '', $currentPath), '/'), '/') + 1;
    } elseif (strpos($currentPath, '/public/') !== false) {
        $depth = substr_count(trim(str_replace('/public/', '', $currentPath), '/'), '/') + 1;
    }
    
    // Build relative path back to root
    $relativePath = str_repeat('../', $depth);
    
    return $relativePath;
}

// Get base path for assets and links
$BASE_URL = getBaseUrl();

// Define common paths
define('ASSETS_PATH', $BASE_URL . 'assets/');
define('CSS_PATH', ASSETS_PATH . 'css/');
define('JS_PATH', ASSETS_PATH . 'js/');
define('IMG_PATH', ASSETS_PATH . 'img/');

// Define site sections
define('ADMIN_PATH', $BASE_URL . 'admin/');
define('PUBLIC_PATH', $BASE_URL . 'public/');
define('AUTH_PATH', $BASE_URL . 'auth/');

// Database paths for public sections
define('WEAPON_PATH', PUBLIC_PATH . 'weapon/');
define('ARMOR_PATH', PUBLIC_PATH . 'armor/');
define('ITEMS_PATH', PUBLIC_PATH . 'items/');
define('DOLLS_PATH', PUBLIC_PATH . 'dolls/');
define('MAPS_PATH', PUBLIC_PATH . 'maps/');
define('MONSTERS_PATH', PUBLIC_PATH . 'monsters/');

// Admin paths
define('ADMIN_WEAPON_PATH', ADMIN_PATH . 'categories/weapon/');
define('ADMIN_ARMOR_PATH', ADMIN_PATH . 'categories/armor/');
?>
