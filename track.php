<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// AWS RDS or local database configuration
$servername = "localhost"; // Change to your AWS RDS endpoint if using RDS
$username = "gleesire_user";
$password = "Sri@123#$#"; // Add your database password
$dbname = "dubai_analytics";
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['error' => 'Database connection failed', 'details' => $e->getMessage()]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

try {
    if ($input['type'] === 'lead') {
        $stmt = $pdo->prepare("INSERT INTO leads (name, phone, date, attraction, adults, children, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $input['name'],
            $input['phone'],
            $input['date'] ?? '',
            $input['attraction'],
            (int)$input['adults'],
            (int)($input['children'] ?: 0),
            time()
        ]);
    
        
        if ($result) {
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['error' => 'Failed to insert lead']);
        }
    } elseif ($input['type'] === 'visit') {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $pdo->prepare("INSERT INTO visitors (ip, city, page, timestamp) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([
            $ip,
            $input['city'],
            $input['page'],
            time()
        ]);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to insert visitor']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request type']);
    }
} catch(PDOException $e) {
    error_log("Database query failed: " . $e->getMessage());
    echo json_encode(['error' => 'Database query failed', 'details' => $e->getMessage()]);
}
?>