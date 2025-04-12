<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'animals' => []];

try {
    $sql = "SELECT * FROM animals WHERE status = 'available' ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result) {
        $response['success'] = true;
        while ($row = $result->fetch_assoc()) {
            $response['animals'][] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'type' => $row['animal_type'],
                'gender' => $row['gender'],
                'age' => $row['age'],
                'description' => $row['description'],
                'image_url' => $row['image_path'],
                'status' => $row['status']
            ];
        }
    } else {
        throw new Exception('Database error: ' . $conn->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>