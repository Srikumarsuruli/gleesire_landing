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
$newsletters = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);

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
        .nav-tabs { display: flex; margin-bottom: 20px; border-bottom: 2px solid #ddd; }
        .nav-tab { padding: 10px 20px; background: #f8f9fa; border: none; cursor: pointer; margin-right: 5px; }
        .nav-tab.active { background: #3fd0d4; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
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
        <div class="stat-box">
            <div class="stat-number"><?= count($newsletters) ?></div>
            <div>Newsletter Subscribers</div>
        </div>
    </div>

    <div class="card">
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('leads')">All Enquiries</button>
            <button class="nav-tab" onclick="showTab('newsletter')">Newsletter Subscribers</button>
        </div>
        
        <div id="leads-tab" class="tab-content active">
            <div class="section-header">
                <h3>All Enquiries</h3>
                <a href="export-csv.php?type=leads" class="download-btn">Download CSV</a>
            </div>
            
            <div class="filters">
                <input type="month" id="monthFilter" placeholder="Filter by Month">
                <input type="date" id="fromDate" placeholder="From Date">
                <input type="date" id="toDate" placeholder="To Date">
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
        
        <div id="newsletter-tab" class="tab-content">
            <div class="section-header">
                <h3>Newsletter Subscribers</h3>
                <a href="export-csv.php?type=newsletter" class="download-btn">Download CSV</a>
            </div>
            
            <div class="filters">
                <input type="month" id="newsletterMonthFilter" placeholder="Filter by Month">
                <input type="date" id="newsletterFromDate" placeholder="From Date">
                <input type="date" id="newsletterToDate" placeholder="To Date">
                <input type="text" id="newsletterSearchBox" class="search-box" placeholder="Search by email...">
                <button onclick="clearNewsletterFilters()">Clear</button>
            </div>
            
            <table id="newsletterTable">
                <tr><th>S.No</th><th>Email</th><th>Subscribed At</th></tr>
                <?php $sno = 1; foreach ($newsletters as $newsletter): ?>
                    <tr>
                        <td><?= $sno++ ?></td>
                        <td><?= htmlspecialchars($newsletter['email'] ?? '') ?></td>
                        <td><?= date('Y-m-d H:i:s', $newsletter['timestamp'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    
    <script>
    function showTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(tab => tab.classList.remove('active'));
        
        // Remove active class from all tabs
        const tabs = document.querySelectorAll('.nav-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        
        // Show selected tab content
        document.getElementById(tabName + '-tab').classList.add('active');
        
        // Add active class to clicked tab
        event.target.classList.add('active');
    }
    
    function filterTable() {
        const monthFilter = document.getElementById('monthFilter').value;
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const searchBox = document.getElementById('searchBox').value.toLowerCase();
        const table = document.getElementById('leadsTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            const dateTime = cells[6].textContent;
            const rowDate = dateTime.split(' ')[0];
            const name = cells[1].textContent.toLowerCase();
            const phone = cells[2].textContent.toLowerCase();
            const attraction = cells[3].textContent.toLowerCase();
            
            let showRow = true;
            
            if (monthFilter && !dateTime.startsWith(monthFilter)) {
                showRow = false;
            }
            
            if (fromDate && rowDate < fromDate) {
                showRow = false;
            }
            
            if (toDate && rowDate > toDate) {
                showRow = false;
            }
            
            if (searchBox && !name.includes(searchBox) && !phone.includes(searchBox) && !attraction.includes(searchBox)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        }
    }
    
    function filterNewsletterTable() {
        const monthFilter = document.getElementById('newsletterMonthFilter').value;
        const fromDate = document.getElementById('newsletterFromDate').value;
        const toDate = document.getElementById('newsletterToDate').value;
        const searchBox = document.getElementById('newsletterSearchBox').value.toLowerCase();
        const table = document.getElementById('newsletterTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            const dateTime = cells[2].textContent;
            const rowDate = dateTime.split(' ')[0];
            const email = cells[1].textContent.toLowerCase();
            
            let showRow = true;
            
            if (monthFilter && !dateTime.startsWith(monthFilter)) {
                showRow = false;
            }
            
            if (fromDate && rowDate < fromDate) {
                showRow = false;
            }
            
            if (toDate && rowDate > toDate) {
                showRow = false;
            }
            
            if (searchBox && !email.includes(searchBox)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        }
    }
    
    function clearFilters() {
        document.getElementById('monthFilter').value = '';
        document.getElementById('fromDate').value = '';
        document.getElementById('toDate').value = '';
        document.getElementById('searchBox').value = '';
        filterTable();
    }
    
    function clearNewsletterFilters() {
        document.getElementById('newsletterMonthFilter').value = '';
        document.getElementById('newsletterFromDate').value = '';
        document.getElementById('newsletterToDate').value = '';
        document.getElementById('newsletterSearchBox').value = '';
        filterNewsletterTable();
    }
    
    // Event listeners for leads table
    document.getElementById('monthFilter').addEventListener('change', filterTable);
    document.getElementById('fromDate').addEventListener('change', filterTable);
    document.getElementById('toDate').addEventListener('change', filterTable);
    document.getElementById('searchBox').addEventListener('input', filterTable);
    
    // Event listeners for newsletter table
    document.getElementById('newsletterMonthFilter').addEventListener('change', filterNewsletterTable);
    document.getElementById('newsletterFromDate').addEventListener('change', filterNewsletterTable);
    document.getElementById('newsletterToDate').addEventListener('change', filterNewsletterTable);
    document.getElementById('newsletterSearchBox').addEventListener('input', filterNewsletterTable);
    </script>
</body>
</html>