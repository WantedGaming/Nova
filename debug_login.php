<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Debug Information</h2>";
echo "<p><strong>SESSION DATA:</strong></p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p><strong>SERVER INFO:</strong></p>";
echo "<pre>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
echo "</pre>";

echo "<p><strong>Current Working Directory:</strong> " . getcwd() . "</p>";

echo "<p><strong>Is user logged in?</strong> " . (isset($_SESSION['user_id']) ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Access Level:</strong> " . (isset($_SESSION['access_level']) ? $_SESSION['access_level'] : 'Not set') . "</p>";
echo "<p><strong>Is Admin?</strong> " . (isset($_SESSION['access_level']) && $_SESSION['access_level'] >= 1 ? 'YES' : 'NO') . "</p>";

if (isset($_SESSION['user_id']) && $_SESSION['access_level'] >= 1) {
    echo "<p><a href='admin/dashboard.php'>Try accessing admin dashboard</a></p>";
} else {
    echo "<p>Please login first from the main page</p>";
}
?>
