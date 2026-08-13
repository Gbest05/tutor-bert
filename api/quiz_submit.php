<?php
// API Endpoint / Form Handler for Submitting Quiz
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_student();

$user = get_logged_user();
$studentId = $user['student_id'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizId = (int)($_POST['quiz_id'] ?? 0);
    $timeTaken = (int)($_POST['time_taken'] ?? 0);
    $userAnswers = $_POST['answers'] ?? [];

    if ($quizId <= 0) {
        header("Location: ../quizzes.php?msg=invalid_quiz");
        exit();
    }

    // Fetch Quiz & Questions
    $qStmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
    $qStmt->execute([$quizId]);
    $questions = $qStmt->fetchAll();

    $totalQuestions = count($questions);
    $correctCount = 0;

    foreach ($questions as $q) {
        $qId = $q['id'];
        $correctOpt = strtoupper(trim($q['correct_option']));
        $selectedOpt = isset($userAnswers[$qId]) ? strtoupper(trim($userAnswers[$qId])) : '';

        if ($selectedOpt === $correctOpt) {
            $correctCount++;
        }
    }

    $percentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

    // Save Quiz Result
    $rStmt = $pdo->prepare("INSERT INTO quiz_results (student_id, quiz_id, score, total, percentage, time_taken_seconds) VALUES (?, ?, ?, ?, ?, ?)");
    $rStmt->execute([$studentId, $quizId, $correctCount, $totalQuestions, $percentage, $timeTaken]);
    $resultId = $pdo->lastInsertId();

    // Update Student Course Progress
    $quizInfoStmt = $pdo->prepare("SELECT course_id FROM quizzes WHERE id = ?");
    $quizInfoStmt->execute([$quizId]);
    $courseId = $quizInfoStmt->fetchColumn();

    if ($courseId) {
        $pStmt = $pdo->prepare("INSERT INTO progress (student_id, course_id, completed_lessons, total_lessons) VALUES (?, ?, 1, 5) ON DUPLICATE KEY UPDATE completed_lessons = LEAST(completed_lessons + 1, total_lessons)");
        $pStmt->execute([$studentId, $courseId]);
    }

    header("Location: ../take_quiz.php?id=$quizId&result_id=$resultId");
    exit();
}
