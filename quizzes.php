<?php
$page_title = "Interactive Quizzes";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$user = get_logged_user();
$studentId = $user['student_id'] ?? 1;

// Fetch Available Quizzes with student's best score
$stmt = $pdo->prepare("
  SELECT q.*, c.title as course_title, c.image as course_image, 
         (SELECT MAX(percentage) FROM quiz_results WHERE quiz_id = q.id AND student_id = ?) as best_score,
         (SELECT COUNT(*) FROM quiz_results WHERE quiz_id = q.id AND student_id = ?) as attempts
  FROM quizzes q 
  JOIN courses c ON q.course_id = c.id 
  ORDER BY q.created_at ASC
");
$stmt->execute([$studentId, $studentId]);
$quizzes = $stmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once 'includes/sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Interactive Quizzes</h3>
          <p class="text-muted small">Evaluate your understanding of Computer Science topics with instant feedback.</p>
        </div>
      </div>

      <div class="row g-4">
        <?php foreach ($quizzes as $quiz): ?>
          <div class="col-lg-6">
            <div class="card-custom p-4 bg-white h-100">
              <div class="d-flex align-items-center gap-3 mb-3">
                <img src="assets/images/<?php echo htmlspecialchars($quiz['course_image']); ?>" class="rounded-3" width="70" height="70" style="object-fit: cover;">
                <div>
                  <span class="badge bg-primary-subtle text-primary mb-1"><?php echo htmlspecialchars($quiz['course_title']); ?></span>
                  <h5 class="fw-bold font-heading mb-0 text-dark"><?php echo htmlspecialchars($quiz['title']); ?></h5>
                </div>
              </div>

              <p class="text-muted small mb-3"><?php echo htmlspecialchars($quiz['description']); ?></p>

              <div class="d-flex flex-wrap gap-3 align-items-center mb-3 p-3 bg-light rounded-3 border">
                <div class="small"><i class="bi bi-clock-history text-primary me-1"></i> Time Limit: <strong><?php echo $quiz['time_limit_mins']; ?> mins</strong></div>
                <div class="small"><i class="bi bi-patch-question-fill text-primary me-1"></i> Questions: <strong><?php echo $quiz['total_questions']; ?> MCQs</strong></div>
                <div class="small ms-auto">
                  <?php if ($quiz['attempts'] > 0): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-trophy-fill me-1"></i> Best Score: <?php echo $quiz['best_score']; ?>%</span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary">Not Attempted</span>
                  <?php endif; ?>
                </div>
              </div>

              <a href="take_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-primary-custom w-100 py-2">
                <i class="bi bi-pencil-square me-1"></i> <?php echo $quiz['attempts'] > 0 ? 'Retake Quiz' : 'Start Quiz Now'; ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</div>

<?php require_once 'includes/portal_footer.php'; ?>
