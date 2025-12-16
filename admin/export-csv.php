<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$type = $_GET['type'] ?? 'leads';

// Set CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

if ($type === 'leads') {
    // Export leads data
    $leads = file_exists('../data/leads.json') ? json_decode(file_get_contents('../data/leads.json'), true) : [];
    
    // CSV headers
    fputcsv($output, ['Name', 'Phone', 'Attraction', 'Adults', 'Children', 'Date', 'Timestamp']);
    
    foreach ($leads as $lead) {
        fputcsv($output, [
            $lead['name'] ?? '',
            $lead['phone'] ?? '',
            $lead['attraction'] ?? '',
            $lead['adults'] ?? '',
            $lead['children'] ?? '',
            date('Y-m-d H:i:s', $lead['timestamp'] ?? 0),
            $lead['timestamp'] ?? 0
        ]);
    }
} else if ($type === 'visitors') {
    // Export visitors data
    $visitors = file_exists('../data/visitors.json') ? json_decode(file_get_contents('../data/visitors.json'), true) : [];
    
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