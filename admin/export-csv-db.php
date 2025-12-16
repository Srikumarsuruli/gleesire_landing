<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$type = $_GET['type'] ?? 'leads';

// Create SQLite database connection
$db = new PDO('sqlite:../data/analytics.db');

// Set CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

if ($type === 'leads') {
    // Export leads data from database
    $stmt = $db->query("SELECT * FROM leads ORDER BY timestamp DESC");
    
    // CSV headers
    fputcsv($output, ['ID', 'Name', 'Phone', 'Attraction', 'Adults', 'Children', 'Date', 'Timestamp']);
    
    while ($lead = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $lead['id'],
            $lead['name'],
            $lead['phone'],
            $lead['attraction'],
            $lead['adults'],
            $lead['children'],
            date('Y-m-d H:i:s', $lead['timestamp']),
            $lead['timestamp']
        ]);
    }
} else if ($type === 'visitors') {
    // Export visitors data from database
    $stmt = $db->query("SELECT * FROM visitors ORDER BY timestamp DESC");
    
    // CSV headers
    fputcsv($output, ['ID', 'IP Address', 'City', 'Page', 'Date', 'Timestamp']);
    
    while ($visitor = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $visitor['id'],
            $visitor['ip'],
            $visitor['city'],
            $visitor['page'],
            date('Y-m-d H:i:s', $visitor['timestamp']),
            $visitor['timestamp']
        ]);
    }
}

fclose($output);
exit;
?>