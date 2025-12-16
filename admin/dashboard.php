<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// Database connection
$servername = "localhost";
$username = "gleesire_user";
$password = "Sri@123#$#";
$dbname = "dubai_analytics";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Read data from database
$visitors = $pdo->query("SELECT * FROM visitors ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);
$leads = $pdo->query("SELECT * FROM leads ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);

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
        .filters { display: flex; gap: 15px; margin-bottom: 20px; align-items: center; }
        .filters input, .filters select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .search-box { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 250px; }
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
            <div>Website Hits</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= count($leads) ?></div>
            <div>Total Leads</div>
        </div>
    </div>

    <div class="card">
        <div class="section-header">
            <h3>All Enquiries</h3>
            <a href="export-csv.php?type=leads" class="download-btn">Download CSV</a>
        </div>
        
        <div class="filters">
            <input type="month" id="monthFilter" placeholder="Filter by Month">
            <input type="date" id="dateFilter" placeholder="Filter by Date">
            <input type="text" id="searchBox" class="search-box" placeholder="Search by name, phone, or attraction...">
            <button onclick="clearFilters()">Clear</button>
        </div>
        
        <table id="leadsTable">
            <tr><th>S.No</th><th>Name</th><th>Phone</th><th>Attraction</th><th>Adults</th><th>Children</th><th>Date & Time</th></tr>
            <?php $sno = 1; foreach ($leads as $lead): ?>
                <tr>
                    <td><?= $sno++ ?></td>
                    <td><?= htmlspecialchars($lead['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['phone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($lead['attraction'] ?? '') ?></td>
                    <td><?= $lead['adults'] ?? 0 ?></td>
                    <td><?= $lead['children'] ?? 0 ?></td>
                    <td><?= date('Y-m-d H:i:s', $lead['timestamp'] ?? 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <script>
    function filterTable() {
        const monthFilter = document.getElementById('monthFilter').value;
        const dateFilter = document.getElementById('dateFilter').value;
        const searchBox = document.getElementById('searchBox').value.toLowerCase();
        const table = document.getElementById('leadsTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            const dateTime = cells[6].textContent;
            const name = cells[1].textContent.toLowerCase();
            const phone = cells[2].textContent.toLowerCase();
            const attraction = cells[3].textContent.toLowerCase();
            
            let showRow = true;
            
            if (monthFilter && !dateTime.startsWith(monthFilter)) {
                showRow = false;
            }
            
            if (dateFilter && !dateTime.startsWith(dateFilter)) {
                showRow = false;
            }
            
            if (searchBox && !name.includes(searchBox) && !phone.includes(searchBox) && !attraction.includes(searchBox)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        }
    }
    
    function clearFilters() {
        document.getElementById('monthFilter').value = '';
        document.getElementById('dateFilter').value = '';
        document.getElementById('searchBox').value = '';
        filterTable();
    }
    
    document.getElementById('monthFilter').addEventListener('change', filterTable);
    document.getElementById('dateFilter').addEventListener('change', filterTable);
    document.getElementById('searchBox').addEventListener('input', filterTable);
    </script>
</body>
</html>