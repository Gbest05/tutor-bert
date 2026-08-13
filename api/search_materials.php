<?php
// API Endpoint for Searching & Filtering Learning Materials
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

$query = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = "SELECT lm.*, c.title as course_title FROM learning_materials lm JOIN courses c ON lm.course_id = c.id WHERE 1=1";
$params = [];

if (!empty($query)) {
    $sql .= " AND (lm.title LIKE ? OR lm.category LIKE ? OR c.title LIKE ?)";
    $searchTerm = "%$query%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($category) && $category !== 'all') {
    $sql .= " AND lm.type = ?";
    $params[] = $category;
}

$sql .= " ORDER BY lm.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materials = $stmt->fetchAll();

echo json_encode(['success' => true, 'materials' => $materials]);
