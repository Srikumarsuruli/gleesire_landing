<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['email'])) {
    echo json_encode(['success' => false, 'error' => 'Email is required']);
    exit;
}

$email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
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
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email already subscribed']);
        exit;
    }
    
    // Insert new subscription
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, timestamp) VALUES (?, ?)");
    $stmt->execute([$email, time()]);
    
    echo json_encode(['success' => true, 'message' => 'Successfully subscribed to newsletter']);
    
} catch(PDOException $e) {
    error_log("Newsletter subscription error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>