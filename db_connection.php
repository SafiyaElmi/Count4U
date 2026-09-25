<?php

$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
    die("Database connection failed: DATABASE_URL is not configured.");
}

try {
    $conn = new PDO($databaseUrl, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => true
    ]);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>