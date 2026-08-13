<?php
$page_title = "Admin Dashboard";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_admin();

// System Statistics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalQuestions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
$totalQuizzes = $pdo->query("SELECT COUNT(*) FROM quiz_results")->fetchColumn();
$avgStudentScore = round($pdo->query("SELECT COALESCE(AVG(percentage), 0) FROM quiz_results")->fetchColumn(), 1);

// Recent Questions Log
$recentQuestions = $pdo->query("
  SELECT q.question_text, q.created_at, u.name as student_name, a.bert_confidence, a.response_text 
  FROM questions q 
  JOIN students s ON q.student_id = s.id 
  JOIN users u ON s.user_id = u.id 
  LEFT JOIN ai_responses a ON q.id = a.question_id 
  ORDER BY q.created_at DESC LIMIT 5
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">System Overview & Analytics</h3>
          <p class="text-muted small">Monitor Intelligent Tutoring System platform metrics.</p>
        </div>
      </div>

      <!-- Stat Cards Grid -->
      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small fw-medium">Total Students</span>
                <h3 class="fw-bold mb-0 font-heading text-dark mt-1"><?php echo $totalStudents; ?></h3>
              </div>
              <div class="feature-icon-box m-0 bg-primary-subtle text-primary"><i class="bi bi-people-fill"></i></div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small fw-medium">Active Courses</span>
                <h3 class="fw-bold mb-0 font-heading text-dark mt-1"><?php echo $totalCourses; ?></h3>
              </div>
              <div class="feature-icon-box m-0 bg-info-subtle text-info"><i class="bi bi-journal-bookmark-fill"></i></div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small fw-medium">Questions Answered</span>
                <h3 class="fw-bold mb-0 font-heading text-dark mt-1"><?php echo $totalQuestions; ?></h3>
              </div>
              <div class="feature-icon-box m-0 bg-success-subtle text-success"><i class="bi bi-chat-left-quote-fill"></i></div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="card-custom p-3 bg-white">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted small fw-medium">Avg Quiz Score</span>
                <h3 class="fw-bold mb-0 font-heading text-dark mt-1"><?php echo $avgStudentScore; ?>%</h3>
              </div>
              <div class="feature-icon-box m-0 bg-warning-subtle text-warning"><i class="bi bi-star-fill"></i></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Analytics Chart -->
        <div class="col-lg-7">
          <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-graph-up-arrow text-primary me-2"></i> System Activity Overview</h5>
            <canvas id="adminChart" height="200"></canvas>
          </div>
        </div>

        <!-- Questions Activity -->
        <div class="col-lg-5">
          <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-cpu-fill text-accent me-2"></i> Recent BERT Queries</h5>
            <div class="d-flex flex-column gap-3">
              <?php foreach ($recentQuestions as $rq): ?>
                <div class="p-3 bg-light rounded-3 border">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="small text-dark"><?php echo htmlspecialchars($rq['student_name']); ?></strong>
                    <span class="bert-badge"><i class="bi bi-cpu"></i> <?php echo $rq['bert_confidence'] ?? 95; ?>%</span>
                  </div>
                  <p class="small text-muted mb-0">Q: <?php echo htmlspecialchars($rq['question_text']); ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const ctx = document.getElementById('adminChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      datasets: [{
        label: 'Student Queries',
        data: [12, 19, 15, 25, 22, 30, 35],
        borderColor: '#2563eb',
        tension: 0.3,
        fill: true,
        backgroundColor: 'rgba(37, 99, 235, 0.1)'
      }, {
        label: 'Quizzes Taken',
        data: [5, 8, 12, 10, 18, 14, 20],
        borderColor: '#14b8a6',
        tension: 0.3
      }]
    },
    options: { responsive: true }
  });
});
</script>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
