<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle report status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reportId = intval($_POST['report_id']);
    $newStatus = $_POST['new_status'];
    
    $stmt = $conn->prepare("UPDATE reported_animals SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $reportId);
    $stmt->execute();
    
    header("Location: reported.php?updated=1");
    exit;
}

// Get all reported animals
$reports = $conn->query("SELECT * FROM reported_animals ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reported Animals</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (same as dashboard.php) -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <h2 class="text-2xl font-bold mb-6">Reported Animals</h2>

            <?php if (isset($_GET['updated'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Report status updated successfully!
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (count($reports) > 0): ?>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img src="<?= htmlspecialchars($report['image_path']) ?>" alt="Reported animal" class="h-10 w-10 rounded-full object-cover">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= ucfirst(htmlspecialchars($report['animal_type'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= ucfirst(htmlspecialchars($report['condition_status'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?= htmlspecialchars(substr($report['location'], 0, 20)) ?>...
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= $report['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($report['status'] === 'rescued' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                                                <?= ucfirst(htmlspecialchars($report['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= date('M j, Y', strtotime($report['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                                <select name="new_status" onchange="this.form.submit()" class="border rounded p-1 text-sm">
                                                    <option value="pending" <?= $report['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="rescued" <?= $report['status'] === 'rescued' ? 'selected' : '' ?>>Rescued</option>
                                                    <option value="rejected" <?= $report['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                            <a href="view_report.php?id=<?= $report['id'] ?>" class="text-blue-600 hover:text-blue-900 ml-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No reports found.
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