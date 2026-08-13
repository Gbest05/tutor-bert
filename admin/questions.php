<?php
$page_title = "Question Logs & BERT AI";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_admin();

// Fetch Questions & AI Responses
$questions = $pdo->query("
  SELECT q.id, q.question_text, q.created_at, u.name as student_name, s.student_id_code,
         a.response_text, a.bert_confidence, a.processing_time_ms
  FROM questions q 
  JOIN students s ON q.student_id = s.id 
  JOIN users u ON s.user_id = u.id 
  LEFT JOIN ai_responses a ON q.id = a.question_id 
  ORDER BY q.created_at DESC
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
          <h3 class="fw-bold font-heading text-dark mb-1">Student Questions & BERT Logs</h3>
          <p class="text-muted small">Monitor questions asked by students and BERT response confidence metrics.</p>
        </div>
      </div>

      <div class="card-custom p-4 bg-white">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light small">
              <tr>
                <th>Date</th>
                <th>Student</th>
                <th>Question Asked</th>
                <th>BERT Response</th>
                <th>BERT Confidence</th>
                <th>Latency</th>
              </tr>
            </thead>
            <tbody class="small">
              <?php foreach ($questions as $q): ?>
                <tr>
                  <td class="text-muted text-nowrap"><?php echo date('M d, h:i A', strtotime($q['created_at'])); ?></td>
                  <td>
                    <strong class="d-block text-dark"><?php echo htmlspecialchars($q['student_name']); ?></strong>
                    <small class="text-muted"><?php echo htmlspecialchars($q['student_id_code']); ?></small>
                  </td>
                  <td class="fw-semibold text-dark" style="max-width: 220px;"><?php echo htmlspecialchars($q['question_text']); ?></td>
                  <td style="max-width: 320px;"><?php echo htmlspecialchars(substr($q['response_text'] ?? 'N/A', 0, 120)) . '...'; ?></td>
                  <td>
                    <span class="bert-badge"><i class="bi bi-cpu me-1"></i> <?php echo number_format($q['bert_confidence'] ?? 95, 2); ?>%</span>
                  </td>
                  <td><span class="badge bg-light text-dark border"><?php echo $q['processing_time_ms'] ?? 180; ?> ms</span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
