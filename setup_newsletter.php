<?php
// Database setup script for newsletter functionality
$servername = "localhost";
$username = "gleesire_user";
$password = "Sri@123#$#";
$dbname = "dubai_analytics";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create newsletter_subscribers table
    $sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        timestamp INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Newsletter subscribers table created successfully!<br>";
    echo "Newsletter functionality is now ready to use.<br>";
    echo "<a href='index.html'>Go to Homepage</a> | <a href='admin/'>Go to Admin Panel</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>