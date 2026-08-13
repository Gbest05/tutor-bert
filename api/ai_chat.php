<?php
// API Endpoint for AI Tutor Chat & BERT Processing
error_reporting(0);
ini_set('display_errors', 0);
if (function_exists('ob_start')) {
    @ob_start();
}

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/BertNLPEngine.php';

// Clean any buffer before rendering JSON output
if (function_exists('ob_clean')) {
    @ob_clean();
}

$user = get_logged_user();
$studentId = $user['student_id'] ?? 0;

// Handle Feedback Action
if (isset($_GET['action']) && $_GET['action'] === 'feedback') {
    $input = json_decode(file_get_contents('php://input'), true);
    $historyId = (int)($input['history_id'] ?? 0);
    $feedback = in_array($input['feedback'] ?? '', ['like', 'dislike']) ? $input['feedback'] : 'none';

    if ($historyId > 0 && isset($pdo) && $pdo !== null) {
        try {
            $stmt = $pdo->prepare("UPDATE chatbot_history SET user_feedback = ? WHERE id = ?");
            $stmt->execute([$feedback, $historyId]);
        } catch (Exception $e) {}
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle AI Chat Question
$input = json_decode(file_get_contents('php://input'), true);
$question = trim($input['question'] ?? ($_POST['question'] ?? ($_GET['question'] ?? '')));

if (empty($question)) {
    echo json_encode(['success' => false, 'error' => 'Please type a question for BERT AI Tutor.']);
    exit();
}

// Execute BERT NLP Pipeline (Works both online & offline even if DB is not yet set up)
$result = BertNLPEngine::processQuery($question, $studentId);

if ($result && isset($result['success']) && $result['success'] === true) {
    $response = $result['response'];
    $confidence = $result['bert_confidence'] ?? 96.0;
    $procTime = $result['processing_time_ms'] ?? 180;
    $course = $result['recommended_course'] ?? 'Computer Science';
    $historyId = rand(100, 999);

    // If Database is connected, log interactions into MySQL
    if (isset($pdo) && $pdo !== null) {
        try {
            // 1. Log Question
            $qStmt = $pdo->prepare("INSERT INTO questions (student_id, question_text) VALUES (?, ?)");
            $qStmt->execute([$studentId > 0 ? $studentId : 1, $question]);
            $questionId = $pdo->lastInsertId();

            // 2. Log AI Response
            $aStmt = $pdo->prepare("INSERT INTO ai_responses (question_id, response_text, bert_confidence, processing_time_ms) VALUES (?, ?, ?, ?)");
            $aStmt->execute([$questionId, $response, $confidence, $procTime]);

            // 3. Log Chatbot History
            $cStmt = $pdo->prepare("INSERT INTO chatbot_history (student_id, user_message, bot_response, bert_confidence) VALUES (?, ?, ?, ?)");
            $cStmt->execute([$studentId > 0 ? $studentId : 1, $question, $response, $confidence]);
            $historyId = $pdo->lastInsertId();
        } catch (Exception $e) {
            // DB log failed silently, return answer to student without error
        }
    } else {
        // DB not connected notice appended if DB is missing
        if (!empty($db_connection_error)) {
            $response .= "\n\n⚠️ *Server Notice: Database is not connected yet (" . htmlspecialchars($db_connection_error) . "). Please configure `config/db.php` on InfinityFree.*";
        }
    }

    echo json_encode([
        'success' => true,
        'response' => $response,
        'bert_confidence' => $confidence,
        'processing_time_ms' => $procTime,
        'recommended_course' => $course,
        'history_id' => $historyId
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'BERT engine failed to evaluate input']);
}
