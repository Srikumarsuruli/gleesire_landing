<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// Create SQLite database
$db = new PDO('sqlite:../data/analytics.db');
$db->exec("CREATE TABLE IF NOT EXISTS visitors (id INTEGER PRIMARY KEY, ip TEXT, city TEXT, page TEXT, timestamp INTEGER)");
$db->exec("CREATE TABLE IF NOT EXISTS leads (id INTEGER PRIMARY KEY, name TEXT, phone TEXT, attraction TEXT, adults INTEGER, children INTEGER, timestamp INTEGER)");

// Get stats
$totalVisits = $db->query("SELECT COUNT(*) FROM visitors")->fetchColumn();
$totalLeads = $db->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$cityStats = $db->query("SELECT city, COUNT(*) as count FROM visitors GROUP BY city")->fetchAll(PDO::FETCH_ASSOC);
$recentLeads = $db->query("SELECT * FROM leads ORDER BY timestamp DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

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
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <a href="?logout=1" class="logout">Logout</a>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-number"><?= $totalVisits ?></div>
            <div>Total Visits</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= $totalLeads ?></div>
            <div>Total Leads</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= count($cityStats) ?></div>
            <div>Cities</div>
        </div>
    </div>

    <div class="card">
        <h3>Visitors by City</h3>
        <table>
            <tr><th>City</th><th>Visits</th></tr>
            <?php foreach ($cityStats as $stat): ?>
                <tr><td><?= htmlspecialchars($stat['city']) ?></td><td><?= $stat['count'] ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h3>Recent Leads</h3>
        <table>
            <tr><th>Name</th><th>Phone</th><th>Attraction</th><th>Date</th></tr>
            <?php foreach ($recentLeads as $lead): ?>
                <tr>
                    <td><?= htmlspecialchars($lead['name']) ?></td>
                    <td><?= htmlspecialchars($lead['phone']) ?></td>
                    <td><?= htmlspecialchars($lead['attraction']) ?></td>
                    <td><?= date('Y-m-d H:i', $lead['timestamp']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>