<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle animal status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $animalId = intval($_POST['animal_id']);
    $newStatus = $_POST['new_status'];
    
    $stmt = $conn->prepare("UPDATE animals SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $animalId);
    $stmt->execute();
    
    header("Location: animals.php?updated=1");
    exit;
}

// Get all animals
$animals = $conn->query("SELECT * FROM animals ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Animals</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (same as dashboard.php) -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Manage Animals</h2>
                <a href="add_animal.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-plus mr-2"></i> Add Animal
                </a>
            </div>

            <?php if (isset($_GET['updated'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Animal status updated successfully!
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($animals) > 0): ?>
                                <?php foreach ($animals as $animal): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img src="<?= htmlspecialchars($animal['image_path']) ?>" alt="<?= htmlspecialchars($animal['name']) ?>" class="h-10 w-10 rounded-full object-cover">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars($animal['name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= ucfirst(htmlspecialchars($animal['animal_type'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= ucfirst(htmlspecialchars($animal['gender'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars($animal['age']) ?> years
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $animal['status'] === 'available' ? 'bg-green-100 text-green-800' : 
                                                   ($animal['status'] === 'adopted' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                                <?= ucfirst(htmlspecialchars($animal['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
                                                <select name="new_status" onchange="this.form.submit()" class="border rounded p-1 text-sm">
                                                    <option value="available" <?= $animal['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                                    <option value="pending" <?= $animal['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="adopted" <?= $animal['status'] === 'adopted' ? 'selected' : '' ?>>Adopted</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                            <a href="edit_animal.php?id=<?= $animal['id'] ?>" class="text-blue-600 hover:text-blue-900 ml-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No animals found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>