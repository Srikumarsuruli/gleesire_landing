<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// Read data files
$visitors = file_exists('../data/visitors.json') ? json_decode(file_get_contents('../data/visitors.json'), true) : [];
$leads = file_exists('../data/leads.json') ? json_decode(file_get_contents('../data/leads.json'), true) : [];

// Count visitors by city
$cityStats = [];
foreach ($visitors as $visitor) {
    $city = $visitor['city'] ?? 'Unknown';
    $cityStats[$city] = ($cityStats[$city] ?? 0) + 1;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 20px; background: #f5f5f5; }
        .header { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-box { background: #3fd0d4; color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .logout { background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; }
        .logout:hover { background: #c82333; }
        .download-btn { background: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin-left: 10px; }
        .download-btn:hover { background: #218838; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <a href="?logout=1" class="logout">Logout</a>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-number"><?= count($visitors) ?></div>
            <div>Total Visits</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= count($leads) ?></div>
            <div>Total Leads</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= count($cityStats) ?></div>
            <div>Cities</div>
        </div>
    </div>

    <div class="card">
        <div class="section-header">
            <h3>All Enquiries</h3>
            <a href="export-csv.php?type=leads" class="download-btn">Download CSV</a>
        </div>
        <table>
            <tr><th>Name</th><th>Phone</th><th>Attraction</th><th>Date</th></tr>
            <?php foreach (array_reverse($leads) as $lead): ?>
                <tr>
                    <td><?= htmlspecialchars($lead['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['phone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['attraction'] ?? '') ?></td>
                    <td><?= date('Y-m-d H:i', $lead['timestamp'] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>