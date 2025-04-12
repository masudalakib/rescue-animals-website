<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle adoption status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $requestId = intval($_POST['request_id']);
    $newStatus = $_POST['new_status'];
    
    $stmt = $conn->prepare("UPDATE adoption_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $requestId);
    $stmt->execute();
    
    // If approved, update animal status
    if ($newStatus === 'approved') {
        $animalId = $conn->query("SELECT animal_id FROM adoption_requests WHERE id = $requestId")->fetch_row()[0];
        $conn->query("UPDATE animals SET status = 'adopted' WHERE id = $animalId");
    }
    
    header("Location: adoptions.php?updated=1");
    exit;
}

// Get all adoption requests with animal details
$requests = $conn->query("
    SELECT ar.*, a.name as animal_name, a.image_path as animal_image 
    FROM adoption_requests ar
    JOIN animals a ON ar.animal_id = a.id
    ORDER BY ar.submitted_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (same as dashboard.php) -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <h2 class="text-2xl font-bold mb-6">Adoption Requests</h2>

            <?php if (isset($_GET['updated'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Adoption request status updated successfully!
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Animal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adopter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($requests) > 0): ?>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img src="<?= htmlspecialchars($request['animal_image']) ?>" alt="<?= htmlspecialchars($request['animal_name']) ?>" class="h-10 w-10 rounded-full object-cover mr-3">
                                                <div>
                                                    <div class="font-medium"><?= htmlspecialchars($request['animal_name']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars($request['full_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($request['email']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($request['phone']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($request['status'] === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                                                <?= ucfirst(htmlspecialchars($request['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M j, Y', strtotime($request['submitted_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                                <select name="new_status" onchange="this.form.submit()" class="border rounded p-1 text-sm">
                                                    <option value="pending" <?= $request['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="approved" <?= $request['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                                    <option value="rejected" <?= $request['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                            <a href="view_request.php?id=<?= $request['id'] ?>" class="text-blue-600 hover:text-blue-900 ml-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No adoption requests found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                   