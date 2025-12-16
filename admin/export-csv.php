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

$type = $_GET['type'] ?? 'leads';

// Set CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

if ($type === 'leads') {
    // Export leads data from database
    $leads = $pdo->query("SELECT * FROM leads ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // CSV headers
    fputcsv($output, ['S.No', 'Name', 'Phone', 'Attraction', 'Adults', 'Children', 'Date & Time']);
    
    $sno = 1;
    foreach ($leads as $lead) {
        fputcsv($output, [
            $sno++,
            $lead['name'] ?? '',
            $lead['phone'] ?? '',
            $lead['attraction'] ?? '',
            $lead['adults'] ?? 0,
            $lead['children'] ?? 0,
            date('Y-m-d H:i:s', $lead['timestamp'] ?? 0)
        ]);
    }
} else if ($type === 'visitors') {
    // Export visitors data from database
    $visitors = $pdo->query("SELECT * FROM visitors ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // CSV headers
    fputcsv($output, ['IP Address', 'City', 'Page', 'Date', 'Timestamp']);
    
    foreach ($visitors as $visitor) {
        fputcsv($output, [
            $visitor['ip'] ?? '',
            $visitor['city'] ?? '',
            $visitor['page'] ?? '',
            date('Y-m-d H:i:s', $visitor['timestamp'] ?? 0),
            $visitor['timestamp'] ?? 0
        ]);
    }
}

fclose($output);
exit;
?>