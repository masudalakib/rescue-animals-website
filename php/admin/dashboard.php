<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get stats for dashboard
$animalsCount = $conn->query("SELECT COUNT(*) FROM animals")->fetch_row()[0];
$reportedCount = $conn->query("SELECT COUNT(*) FROM reported_animals WHERE status = 'pending'")->fetch_row()[0];
$adoptionsCount = $conn->query("SELECT COUNT(*) FROM adoption_requests WHERE status = 'pending'")->fetch_row()[0];
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
            <h1 class="text-xl font-bold mb-8">Admin Dashboard</h1>
            <nav>
                <ul class="space-y-2">
                    <li>
                        <a href="dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700">
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
        <div class="flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6">Dashboard Overview</h2>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="fas fa-paw text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-gray-500">Total Animals</h3>
                            <p class="text-2xl font-bold"><?php echo $animalsCount; ?></p>
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
                            <p class="text-2xl font-bold"><?php echo $reportedCount; ?></p>
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
                            <p class="text-2xl font-bold"><?php echo $adoptionsCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Section -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-bold mb-4">Recent Activity</h3>
                <!-- Activity content would go here -->
                <p class="text-gray-600">Recent reports, adoptions, etc. would be displayed here.</p>
            </div>
        </div>
    </div>
</body>
</html>