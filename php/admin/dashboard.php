<?php
session_start();
require_once '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get stats for dashboard
$animalsCount = $conn->query("SELECT COUNT(*) FROM animals")->fetch_row()[0];
$reportedCount = $conn->query("SELECT COUNT(*) FROM reported_animals WHERE status = 'pending'")->fetch_row()[0];
$adoptionsCount = $conn->query("SELECT COUNT(*) FROM adoption_requests WHERE status = 'pending'")->fetch_row()[0];
$recentAnimals = $conn->query("SELECT * FROM animals ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$recentReports = $conn->query("SELECT * FROM reported_animals ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 p-4">
            <div class="flex items-center mb-8">
                <i class="fas fa-paw text-2xl mr-2"></i>
                <h1 class="text-xl font-bold">Admin Dashboard</h1>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700 bg-gray-700">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="animals.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-paw mr-2"></i> Animals
                        </a>
                    </li>
                    <li>
                        <a href="reported.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-flag mr-2"></i> Reported Animals
                        </a>
                    </li>
                    <li>
                        <a href="adoptions.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-home mr-2"></i> Adoption Requests
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Dashboard Overview</h2>
                <div class="text-gray-600">
                    Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>!
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="fas fa-paw text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-gray-500">Total Animals</h3>
                            <p class="text-2xl font-bold"><?= $animalsCount ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <i class="fas fa-flag text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-gray-500">Pending Reports</h3>
                            <p class="text-2xl font-bold"><?= $reportedCount ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                            <i class="fas fa-home text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-gray-500">Pending Adoptions</h3>
                            <p class="text-2xl font-bold"><?= $adoptionsCount ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Animals -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-bold mb-4">Recently Added Animals</h3>
                    <div class="space-y-4">
                        <?php if (count($recentAnimals) > 0): ?>
                            <?php foreach ($recentAnimals as $animal): ?>
                                <div class="flex items-center p-3 border-b border-gray-100">
                                    <img src="<?= htmlspecialchars($animal['image_path']) ?>" alt="<?= htmlspecialchars($animal['name']) ?>" class="w-12 h-12 rounded-full object-cover mr-3">
                                    <div>
                                        <h4 class="font-semibold"><?= htmlspecialchars($animal['name']) ?></h4>
                                        <p class="text-sm text-gray-600"><?= ucfirst(htmlspecialchars($animal['animal_type'])) ?> • <?= ucfirst(htmlspecialchars($animal['gender'])) ?></p>
                                    </div>
                                    <div class="ml-auto text-sm <?= $animal['status'] === 'available' ? 'text-green-600' : 'text-gray-500' ?>">
                                        <?= ucfirst(htmlspecialchars($animal['status'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-600">No animals found.</p>
                        <?php endif; ?>
                    </div>
                    <a href="animals.php" class="block mt-4 text-green-600 hover:text-green-800 text-sm font-semibold">
                        View all animals <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <!-- Recent Reports -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-bold mb-4">Recent Animal Reports</h3>
                    <div class="space-y-4">
                        <?php if (count($recentReports) > 0): ?>
                            <?php foreach ($recentReports as $report): ?>
                                <div class="flex items-start p-3 border-b border-gray-100">
                                    <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                        <i class="fas fa-<?= $report['animal_type'] === 'dog' ? 'dog' : ($report['animal_type'] === 'cat' ? 'cat' : 'paw') ?> text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold"><?= ucfirst(htmlspecialchars($report['animal_type'])) ?> • <?= ucfirst(htmlspecialchars($report['condition_status'])) ?></h4>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars(substr($report['description'], 0, 50)) ?>...</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($report['location']) ?>
                                        </p>
                                    </div>
                                    <div class="ml-2 text-xs <?= $report['status'] === 'pending' ? 'text-yellow-600' : ($report['status'] === 'rescued' ? 'text-green-600' : 'text-red-600') ?>">
                                        <?= ucfirst(htmlspecialchars($report['status'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-600">No recent reports.</p>
                        <?php endif; ?>
                    </div>
                    <a href="reported.php" class="block mt-4 text-green-600 hover:text-green-800 text-sm font-semibold">
                        View all reports <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>