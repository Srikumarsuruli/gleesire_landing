<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Create SQLite database
    $db = new PDO('sqlite:data/analytics.db');
    $db->exec("CREATE TABLE IF NOT EXISTS visitors (id INTEGER PRIMARY KEY, ip TEXT, city TEXT, page TEXT, timestamp INTEGER)");
    $db->exec("CREATE TABLE IF NOT EXISTS leads (id INTEGER PRIMARY KEY, name TEXT, phone TEXT, attraction TEXT, adults INTEGER, children INTEGER, timestamp INTEGER)");
    
    if ($input['type'] === 'visit') {
        $stmt = $db->prepare("INSERT INTO visitors (ip, city, page, timestamp) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SERVER['REMOTE_ADDR'], $input['city'] ?? 'Unknown', $input['page'] ?? '', time()]);
        
    } elseif ($input['type'] === 'lead') {
        $stmt = $db->prepare("INSERT INTO leads (name, phone, attraction, adults, children, timestamp) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $input['name'] ?? '',
            $input['phone'] ?? '',
            $input['attraction'] ?? '',
            $input['adults'] ?? 0,
            $input['children'] ?? 0,
            time()
        ]);
    }
    
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>