<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate input
    if (empty($_POST['animalType']) || empty($_POST['condition']) || empty($_POST['location'])) {
        throw new Exception('Please fill all required fields.');
    }

    // Handle file upload
    if (empty($_FILES['photo']) {
        throw new Exception('Please upload a photo of the animal.');
    }

    $file = $_FILES['photo'];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('File is too large. Maximum size is 5MB.');
    }
    
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, ALLOWED_TYPES)) {
        throw new Exception('Only JPG, PNG, and GIF files are allowed.');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to save uploaded file.');
    }

    // Prepare data for database
    $animalType = $conn->real_escape_string($_POST['animalType']);
    $condition = $conn->real_escape_string($_POST['condition']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description'] ?? '');
    $imagePath = $conn->real_escape_string($destination);
    $status = 'pending'; // Default status
    $createdAt = date('Y-m-d H:i:s');

    // Insert into database
    $sql = "INSERT INTO reported_animals (animal_type, condition_status, location, description, image_path, status, created_at) 
            VALUES ('$animalType', '$condition', '$location', '$description', '$imagePath', '$status', '$createdAt')";

    if ($conn->query($sql) {
        $response['success'] = true;
        $response['message'] = 'Animal report submitted successfully!';
    } else {
        throw new Exception('Database error: ' . $conn->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>