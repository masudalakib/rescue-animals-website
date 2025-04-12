<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate input
    $requiredFields = ['animalId', 'fullName', 'email', 'phone', 'address'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception('Please fill all required fields.');
        }
    }

    // Prepare data
    $animalId = $conn->real_escape_string($_POST['animalId']);
    $fullName = $conn->real_escape_string($_POST['fullName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $experience = $conn->real_escape_string($_POST['experience'] ?? '');
    $status = 'pending'; // Default status
    $submittedAt = date('Y-m-d H:i:s');

    // Insert into database
    $sql = "INSERT INTO adoption_requests (animal_id, full_name, email, phone, address, experience, status, submitted_at) 
            VALUES ('$animalId', '$fullName', '$email', '$phone', '$address', '$experience', '$status', '$submittedAt')";

    if ($conn->query($sql)) {
        $response['success'] = true;
        $response['message'] = 'Adoption request submitted successfully!';
    } else {
        throw new Exception('Database error: ' . $conn->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>