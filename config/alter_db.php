<?php
require_once __DIR__ . '/db.php';
try {
    $pdo->exec("ALTER TABLE learning_materials MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'pdf'");
    echo "Column type altered successfully.\n";
} catch (Exception $e) {
    echo "Notice: " . $e->getMessage() . "\n";
}
