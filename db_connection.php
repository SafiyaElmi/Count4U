<?php

$host   = getenv('DB_HOST');
$port   = getenv('DB_PORT');
$user   = getenv('DB_USER');
$pass   = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

if (!$host || !$port || !$user || !$pass || !$dbname) {
    die("Database connection failed: Missing environment variables.");
}

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => true
    ]);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>
