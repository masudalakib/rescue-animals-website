<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = intval($_POST['request_id']);
    $newStatus = $_POST['status'];
    
    // Update adoption request status
    $stmt = $conn->prepare("UPDATE adoption_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $requestId);
    $stmt->execute();
    
    // If approved, update animal status
    if ($newStatus === 'approved') {
        $animalId = $conn->query("SELECT animal_id FROM adoption_requests WHERE id = $requestId")->fetch_row()[0];
        $conn->query("UPDATE animals SET status = 'adopted' WHERE id = $animalId");
    }
    
    header('Location: adoptions.php?updated=1');
    exit;
}

header('Location: adoptions.php');
?>