<?php
$page_title = "Learning Progress Tracking";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$user = get_logged_user();
$studentId = $user['student_id'] ?? 1;

// Fetch Quiz Performance History
$qHistStmt = $pdo->prepare("
  SELECT qr.*, q.title as quiz_title, c.title as course_title 
  FROM quiz_results qr 
  JOIN quizzes q ON qr.quiz_id = q.id 
  JOIN courses c ON q.course_id = c.id 
  WHERE qr.student_id = ? 
  ORDER BY qr.completed_at DESC
");
$qHistStmt->execute([$studentId]);
$quizHistory = $qHistStmt->fetchAll();

// Fetch Course Completion Rates
$pStmt = $pdo->prepare("
  SELECT c.title, COALESCE(p.completed_lessons, 0) as completed_lessons, c.total_lessons 
  FROM courses c 
  LEFT JOIN progress p ON c.id = p.course_id AND p.student_id = ?
");
$pStmt->execute([$studentId]);
$courseProgress = $pStmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once 'includes/sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Learning Progress & Analytics</h3>
          <p class="text-muted small">Detailed view of your module completions, quiz scores, and academic performance.</p>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <!-- Visual Chart -->
        <div class="col-lg-7">
          <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Course Completion Rates</h5>
            <canvas id="progressChart" height="200"></canvas>
          </div>
        </div>

        <!-- Quiz Performance Summary -->
        <div class="col-lg-5">
          <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-award-fill text-warning me-2"></i> Performance Milestones</h5>
            <div class="d-flex flex-column gap-3">
              <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                <div>
                  <strong class="d-block text-dark small">Highest Quiz Score</strong>
                  <span class="text-muted small">Database Normalization</span>
                </div>
                <span class="badge bg-success fs-6">95%</span>
              </div>

              <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                <div>
                  <strong class="d-block text-dark small">Total Study Time</strong>
                  <span class="text-muted small">Active on Portal</span>
                </div>
                <span class="badge bg-primary fs-6">14 Hours</span>
              </div>

              <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                <div>
                  <strong class="d-block text-dark small">BERT AI Interactions</strong>
                  <span class="text-muted small">Queries Solved</span>
                </div>
                <span class="badge bg-info fs-6">12 Questions</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quiz Attempts Log Table -->
      <div class="card-custom p-4 bg-white">
        <h5 class="fw-bold font-heading mb-3"><i class="bi bi-clock-history text-primary me-2"></i> Quiz Attempt Logs</h5>
        <?php if (empty($quizHistory)): ?>
          <p class="text-muted small mb-0">No quiz attempts recorded yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light small">
                <tr>
                  <th>Quiz Title</th>
                  <th>Course</th>
                  <th>Score</th>
                  <th>Percentage</th>
                  <th>Completion Date</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php foreach ($quizHistory as $qh): ?>
                  <tr>
                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($qh['quiz_title']); ?></td>
                    <td><?php echo htmlspecialchars($qh['course_title']); ?></td>
                    <td><?php echo $qh['score']; ?> / <?php echo $qh['total']; ?></td>
                    <td>
                      <?php if ($qh['percentage'] >= 70): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle"><?php echo $qh['percentage']; ?>%</span>
                      <?php else: ?>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><?php echo $qh['percentage']; ?>%</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-muted"><?php echo date('M d, Y - h:i A', strtotime($qh['completed_at'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const ctx = document.getElementById('progressChart').getContext('2d');
  
  const labels = <?php echo json_encode(array_column($courseProgress, 'title')); ?>;
  const data = <?php echo json_encode(array_map(function($item) {
    return $item['total_lessons'] > 0 ? round(($item['completed_lessons'] / $item['total_lessons']) * 100) : 0;
  }, $courseProgress)); ?>;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Completion %',
        data: data,
        backgroundColor: '#2563eb',
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, max: 100 }
      }
    }
  });
});
</script>

<?php require_once 'includes/portal_footer.php'; ?>
