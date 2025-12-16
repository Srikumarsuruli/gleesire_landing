<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dubai_analytics";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$leads = $pdo->query("SELECT * FROM leads ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leads Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .count { background: #007cba; color: white; padding: 10px; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Dubai Attractions - Leads Dashboard</h1>
    <div class="count">Total Leads: <?= count($leads) ?></div>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Attraction</th>
            <th>Adults</th>
            <th>Children</th>
            <th>Date Submitted</th>
        </tr>
        <?php foreach($leads as $lead): ?>
        <tr>
            <td><?= $lead['id'] ?></td>
            <td><?= htmlspecialchars($lead['name']) ?></td>
            <td><?= htmlspecialchars($lead['phone']) ?></td>
            <td><?= htmlspecialchars($lead['attraction']) ?></td>
            <td><?= $lead['adults'] ?></td>
            <td><?= $lead['children'] ?></td>
            <td><?= date('Y-m-d H:i:s', $lead['timestamp']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>