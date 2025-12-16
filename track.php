<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dubai_analytics";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if ($input['type'] === 'lead') {
    $stmt = $pdo->prepare("INSERT INTO leads (name, phone, attraction, adults, children, timestamp) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $input['name'],
        $input['phone'],
        $input['attraction'],
        $input['adults'],
        $input['children'] ?: 0,
        time()
    ]);
    echo json_encode(['success' => true]);
} elseif ($input['type'] === 'visit') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO visitors (ip, city, page, timestamp) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $ip,
        $input['city'],
        $input['page'],
        time()
    ]);
    echo json_encode(['success' => true]);
}
?>