<?php
$page_title = "Student Dashboard";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$user = get_logged_user();
$studentId = $user['student_id'] ?? 1;

// Fetch Student Stats
$enrolledCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();

$qCountStmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE student_id = ?");
$qCountStmt->execute([$studentId]);
$questionsAsked = $qCountStmt->fetchColumn();

$quizCountStmt = $pdo->prepare("SELECT COUNT(*), AVG(percentage) FROM quiz_results WHERE student_id = ?");
$quizCountStmt->execute([$studentId]);
$quizRow = $quizCountStmt->fetch();
$quizzesCompleted = $quizRow['COUNT(*)'] ?? 0;
$avgScore = round($quizRow['AVG(percentage)'] ?? 0, 1);

// Fetch Recent Questions Asked
$recentQStmt = $pdo->prepare("
  SELECT q.question_text, q.created_at, a.response_text, a.bert_confidence 
  FROM questions q 
  LEFT JOIN ai_responses a ON q.id = a.question_id 
  WHERE q.student_id = ? 
  ORDER BY q.created_at DESC LIMIT 4
");
$recentQStmt->execute([$studentId]);
$recentQuestions = $recentQStmt->fetchAll();

// Fetch Courses & Student Progress
$progStmt = $pdo->prepare("
  SELECT c.id, c.title, c.category, c.image, c.total_lessons, COALESCE(p.completed_lessons, 0) as completed_lessons 
  FROM courses c 
  LEFT JOIN progress p ON c.id = p.course_id AND p.student_id = ?
");
$progStmt->execute([$studentId]);
$studentCourses = $progStmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once 'includes/sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <!-- Welcome Banner -->
      <div class="card-custom p-4 bg-primary text-white mb-4 border-0 position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center position-relative z-2">
          <div>
            <h3 class="fw-bold mb-1 font-heading">Welcome back, <?php echo htmlspecialchars($user['name']); ?>! 👋</h3>
            <p class="mb-0 text-white-50">Ready to continue your Intelligent Tutoring session with BERT?</p>
          </div>
          <a href="ai_tutor.php" class="btn btn-light text-primary fw-bold mt-3 mt-md-0 shadow-sm">
            <i class="bi bi-robot me-1"></i> Ask BERT a Question
          </a>
        </div>
      </div>

      <!-- Key Stat Cards -->
      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Enrolled Courses</span>
                <h3 class="fw-bold mb-0 text-dark font-heading mt-1"><?php echo $enrolledCount; ?></h3>
              </div>
              <div class="feature-icon-box m-0 bg-primary-subtle text-primary">
                <i class="bi bi-journal-bookmark-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Questions Asked</span>
                <h3 class="fw-bold mb-0 text-dark font-heading mt-1"><?php echo $questionsAsked; ?></h3>
              </div>
              <div class="feature-icon-box m-0 bg-info-subtle text-info">
                <i class="bi bi-chat-left-text-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Quizzes Completed</span>
                <h3 class="fw-bold mb-0 text-dark font-heading mt-1"><?php echo $quizzesCompleted; ?></h3>
              </div>
              <div class="feature-icon-box m-0 bg-success-subtle text-success">
                <i class="bi bi-check-circle-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Average Quiz Score</span>
                <h3 class="fw-bold mb-0 text-dark font-heading mt-1"><?php echo $avgScore; ?>%</h3>
              </div>
              <div class="feature-icon-box m-0 bg-warning-subtle text-warning">
                <i class="bi bi-star-fill"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Learning Progress Tracker -->
        <div class="col-lg-7">
          <div class="card-custom p-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="fw-bold font-heading mb-0"><i class="bi bi-graph-up text-primary me-2"></i> Course Completion Progress</h5>
              <a href="courses.php" class="small text-primary fw-semibold">View All</a>
            </div>

            <div class="d-flex flex-column gap-3">
              <?php foreach ($studentCourses as $c): 
                $pct = $c['total_lessons'] > 0 ? round(($c['completed_lessons'] / $c['total_lessons']) * 100) : 0;
              ?>
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-semibold small text-dark"><?php echo htmlspecialchars($c['title']); ?></span>
                    <span class="small fw-bold text-primary"><?php echo $pct; ?>% (<?php echo $c['completed_lessons']; ?>/<?php echo $c['total_lessons']; ?>)</span>
                  </div>
                  <div class="progress" style="height: 10px; border-radius: 10px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $pct; ?>%; border-radius: 10px;"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Recent Questions Asked -->
          <div class="card-custom p-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="fw-bold font-heading mb-0"><i class="bi bi-clock-history text-primary me-2"></i> Recent AI Tutor Activity</h5>
              <a href="ai_tutor.php" class="small text-primary fw-semibold">Open AI Tutor</a>
            </div>

            <?php if (empty($recentQuestions)): ?>
              <div class="text-center py-4 text-muted small">
                <i class="bi bi-chat-square-dots fs-2 text-secondary mb-2 d-block"></i>
                You haven't asked any questions yet. Start asking BERT!
              </div>
            <?php else: ?>
              <div class="d-flex flex-column gap-3">
                <?php foreach ($recentQuestions as $rq): ?>
                  <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <strong class="text-dark small"><i class="bi bi-question-circle text-primary me-1"></i> <?php echo htmlspecialchars($rq['question_text']); ?></strong>
                      <span class="bert-badge"><i class="bi bi-cpu"></i> <?php echo $rq['bert_confidence'] ?? 96; ?>%</span>
                    </div>
                    <p class="text-muted small mb-0 text-truncate"><?php echo htmlspecialchars($rq['response_text']); ?></p>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Right Side: Recommended Courses & Quick Links -->
        <div class="col-lg-5">
          <div class="card-custom p-4 bg-white mb-4">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-award text-primary me-2"></i> Recommended Modules</h5>
            <div class="d-flex flex-column gap-3">
              <?php foreach (array_slice($studentCourses, 0, 3) as $rc): ?>
                <div class="d-flex gap-3 align-items-center p-2 rounded-3 border">
                  <img src="assets/images/<?php echo htmlspecialchars($rc['image']); ?>" class="rounded-2" width="70" height="60" style="object-fit: cover;">
                  <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1 small text-dark"><?php echo htmlspecialchars($rc['title']); ?></h6>
                    <small class="text-muted d-block"><?php echo htmlspecialchars($rc['category']); ?></small>
                  </div>
                  <a href="course_view.php?id=<?php echo $rc['id']; ?>" class="btn btn-outline-primary btn-sm px-2 py-1"><i class="bi bi-arrow-right"></i></a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-lightning-charge text-warning me-2"></i> Quick Actions</h5>
            <div class="d-grid gap-2">
              <a href="ai_tutor.php" class="btn btn-primary-custom text-start py-2">
                <i class="bi bi-robot me-2"></i> Ask Question to BERT AI
              </a>
              <a href="quizzes.php" class="btn btn-outline-primary text-start py-2">
                <i class="bi bi-file-earmark-check me-2"></i> Take a Computer Science Quiz
              </a>
              <a href="materials.php" class="btn btn-outline-secondary text-start py-2">
                <i class="bi bi-download me-2"></i> Download Lecture Notes
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once 'includes/portal_footer.php'; ?>
