<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate input
    $requiredFields = ['name', 'email', 'subject', 'message'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception('Please fill all required fields.');
        }
    }

    // Prepare data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);
    $createdAt = date('Y-m-d H:i:s');

    // Insert into database
    $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at) 
            VALUES ('$name', '$email', '$subject', '$message', '$createdAt')";

    if ($conn->query($sql)) {
        $response['success'] = true;
        $response['message'] = 'Your message has been sent successfully!';
        
        // Here you could also add email sending functionality
    } else {
        throw new Exception('Database error: ' . $conn->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>