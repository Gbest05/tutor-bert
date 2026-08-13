<?php
// Fast setup script to initialize XAMPP MySQL database for ITS-BERT

$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    echo "Connecting to MySQL server on $host...\n";
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "Creating database if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `its_bert_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `its_bert_db`;");

    echo "Reading database.sql...\n";
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Split SQL into individual statements
    $queries = preg_split('/;\s*$/m', $sql);
    
    $count = 0;
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                $count++;
            } catch (Exception $e) {
                // Ignore comment errors or minor duplicate warnings
            }
        }
    }
    
    echo "SUCCESS: Database `its_bert_db` initialized with $count executed statements!\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
