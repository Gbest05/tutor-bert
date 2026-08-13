<?php
$page_title = "Take Quiz";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$user = get_logged_user();
$studentId = $user['student_id'] ?? 1;

$quizId = (int)($_GET['id'] ?? 1);
$resultId = isset($_GET['result_id']) ? (int)$_GET['result_id'] : null;

// Fetch Quiz Info
$qStmt = $pdo->prepare("SELECT q.*, c.title as course_title FROM quizzes q JOIN courses c ON q.course_id = c.id WHERE q.id = ?");
$qStmt->execute([$quizId]);
$quiz = $qStmt->fetch();

if (!$quiz) {
    header("Location: quizzes.php");
    exit();
}

// Fetch Quiz Questions
$qqStmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY id ASC");
$qqStmt->execute([$quizId]);
$questions = $qqStmt->fetchAll();

// If Result ID present, fetch score details
$quizResult = null;
if ($resultId) {
    $resStmt = $pdo->prepare("SELECT * FROM quiz_results WHERE id = ? AND student_id = ?");
    $resStmt->execute([$resultId, $studentId]);
    $quizResult = $resStmt->fetch();
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once 'includes/sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      
      <?php if ($quizResult): ?>
        <!-- SCORE RESULT BREAKDOWN DISPLAY -->
        <div class="card-custom p-4 p-md-5 bg-white mb-4 text-center">
          <div class="mb-3">
            <?php if ($quizResult['percentage'] >= 70): ?>
              <div class="feature-icon-box mx-auto bg-success-subtle text-success" style="width: 72px; height: 72px; font-size: 2.2rem;">
                <i class="bi bi-trophy-fill"></i>
              </div>
              <h2 class="fw-bold font-heading text-success mt-3 mb-1">Excellent Performance! 🎉</h2>
              <p class="text-muted">You scored <strong><?php echo $quizResult['score']; ?> out of <?php echo $quizResult['total']; ?></strong> (<?php echo $quizResult['percentage']; ?>%). Keep up the great work!</p>
            <?php else: ?>
              <div class="feature-icon-box mx-auto bg-warning-subtle text-warning" style="width: 72px; height: 72px; font-size: 2.2rem;">
                <i class="bi bi-exclamation-circle-fill"></i>
              </div>
              <h2 class="fw-bold font-heading text-dark mt-3 mb-1">Good Effort! 👍</h2>
              <p class="text-muted">You scored <strong><?php echo $quizResult['score']; ?> out of <?php echo $quizResult['total']; ?></strong> (<?php echo $quizResult['percentage']; ?>%). Review the explanations below and try again.</p>
            <?php endif; ?>
          </div>

          <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="take_quiz.php?id=<?php echo $quizId; ?>" class="btn btn-primary-custom px-4"><i class="bi bi-arrow-repeat me-1"></i> Retake Quiz</a>
            <a href="quizzes.php" class="btn btn-outline-secondary px-4"><i class="bi bi-grid me-1"></i> Back to Quizzes</a>
            <a href="ai_tutor.php" class="btn btn-accent-custom px-4"><i class="bi bi-robot me-1"></i> Ask BERT for Help</a>
          </div>
        </div>

        <!-- Question Answer Explanations -->
        <div class="card-custom p-4 bg-white">
          <h5 class="fw-bold font-heading mb-4"><i class="bi bi-journal-check text-primary me-2"></i> Detailed Question Explanations</h5>
          <div class="d-flex flex-column gap-4">
            <?php foreach ($questions as $idx => $q): ?>
              <div class="p-3 bg-light rounded-3 border">
                <h6 class="fw-bold text-dark mb-2">Q<?php echo $idx + 1; ?>: <?php echo htmlspecialchars($q['question_text']); ?></h6>
                <div class="row g-2 mb-2 small">
                  <div class="col-md-6"><span class="badge bg-white text-dark border w-100 p-2 text-start">A) <?php echo htmlspecialchars($q['option_a']); ?></span></div>
                  <div class="col-md-6"><span class="badge bg-white text-dark border w-100 p-2 text-start">B) <?php echo htmlspecialchars($q['option_b']); ?></span></div>
                  <div class="col-md-6"><span class="badge bg-white text-dark border w-100 p-2 text-start">C) <?php echo htmlspecialchars($q['option_c']); ?></span></div>
                  <div class="col-md-6"><span class="badge bg-white text-dark border w-100 p-2 text-start">D) <?php echo htmlspecialchars($q['option_d']); ?></span></div>
                </div>
                <div class="p-2 bg-success-subtle text-success-emphasis rounded border border-success-subtle small fw-medium">
                  <i class="bi bi-check-circle-fill me-1"></i> <strong>Correct Answer:</strong> Option <?php echo $q['correct_option']; ?><br>
                  <em>Explanation: <?php echo htmlspecialchars($q['explanation']); ?></em>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      <?php else: ?>

        <!-- ACTIVE QUIZ TAKING ENVIRONMENT -->
        <div class="card-custom p-4 bg-white mb-4">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1"><?php echo htmlspecialchars($quiz['course_title']); ?></span>
              <h4 class="fw-bold font-heading text-dark mb-0"><?php echo htmlspecialchars($quiz['title']); ?></h4>
            </div>

            <!-- Timer Badge -->
            <div class="d-flex align-items-center gap-2 bg-secondary text-white px-3 py-2 rounded-pill shadow-sm">
              <i class="bi bi-clock-history text-warning fs-5"></i>
              <span class="fw-bold font-heading fs-5" id="quizTimer" data-seconds="<?php echo $quiz['time_limit_mins'] * 60; ?>"><?php echo sprintf('%02d:00', $quiz['time_limit_mins']); ?></span>
            </div>
          </div>
        </div>

        <form action="api/quiz_submit.php" method="POST" id="quizForm">
          <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>">
          <input type="hidden" name="time_taken" id="timeTakenInput" value="0">

          <div class="d-flex flex-column gap-4">
            <?php foreach ($questions as $index => $q): ?>
              <div class="card-custom p-4 bg-white">
                <div class="d-flex align-items-center gap-2 mb-3">
                  <span class="badge bg-primary rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem;"><?php echo $index + 1; ?></span>
                  <h6 class="fw-bold font-heading text-dark mb-0 fs-6"><?php echo htmlspecialchars($q['question_text']); ?></h6>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="quiz-option-label d-flex align-items-center gap-3 p-3 rounded-3 border bg-light w-100 cursor-pointer">
                      <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A" required class="form-check-input">
                      <span><strong>A)</strong> <?php echo htmlspecialchars($q['option_a']); ?></span>
                    </label>
                  </div>

                  <div class="col-md-6">
                    <label class="quiz-option-label d-flex align-items-center gap-3 p-3 rounded-3 border bg-light w-100 cursor-pointer">
                      <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B" required class="form-check-input">
                      <span><strong>B)</strong> <?php echo htmlspecialchars($q['option_b']); ?></span>
                    </label>
                  </div>

                  <div class="col-md-6">
                    <label class="quiz-option-label d-flex align-items-center gap-3 p-3 rounded-3 border bg-light w-100 cursor-pointer">
                      <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C" required class="form-check-input">
                      <span><strong>C)</strong> <?php echo htmlspecialchars($q['option_c']); ?></span>
                    </label>
                  </div>

                  <div class="col-md-6">
                    <label class="quiz-option-label d-flex align-items-center gap-3 p-3 rounded-3 border bg-light w-100 cursor-pointer">
                      <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D" required class="form-check-input">
                      <span><strong>D)</strong> <?php echo htmlspecialchars($q['option_d']); ?></span>
                    </label>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="card-custom p-4 bg-white mt-4 text-end">
            <button type="submit" class="btn btn-primary-custom btn-lg px-5">
              <i class="bi bi-send-check-fill me-2"></i> Submit Quiz Responses
            </button>
          </div>
        </form>

        <script src="assets/js/quiz.js"></script>

      <?php endif; ?>

    </div>
  </main>
</div>

<?php require_once 'includes/portal_footer.php'; ?>
