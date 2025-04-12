<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root'); // default XAMPP username
define('DB_PASSWORD', '');     // default XAMPP password is empty
define('DB_NAME', 'animal_rescue');

// File upload configuration
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Create database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Test query to verify animals table exists
$testQuery = $conn->query("SHOW TABLES LIKE 'animals'");
if ($testQuery->num_rows == 0) {
    die("Error: The 'animals' table doesn't exist in the database. Please run the SQL setup script.");
}
?>