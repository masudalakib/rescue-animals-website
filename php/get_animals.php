<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'animals' => [], 'message' => ''];

try {
    // Check if animals table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'animals'");
    if ($tableCheck->num_rows == 0) {
        throw new Exception("Animals table doesn't exist in the database.");
    }

    // Get available animals
    $sql = "SELECT * FROM animals WHERE status = 'available' ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $response['success'] = true;
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Ensure image path is correct
            $imagePath = $row['image_path'];
            if (!file_exists($imagePath) {
                $imagePath = 'images/default-animal.jpg'; // fallback image
            }

            $response['animals'][] = [
                'id' => $row['id'],
                'name' => $row['name'] ?: 'Unnamed',
                'type' => $row['animal_type'] ?: 'Unknown',
                'gender' => $row['gender'] ?: 'unknown',
                'age' => $row['age'] ?: 'Unknown',
                'description' => $row['description'] ?: 'No description available.',
                'image_url' => $imagePath,
                'status' => $row['status'] ?: 'available'
            ];
        }
    } else {
        $response['message'] = 'No animals available for adoption at the moment.';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log($e->getMessage()); // Log the error for debugging
}

echo json_encode($response);
$conn->close();
?>