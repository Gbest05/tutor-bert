<?php
$page_title = "Manage Quizzes & Question Banks";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_admin();

$msg = '';
$errorMsg = '';

// Add Single Question to Quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_quiz_question'])) {
    $quizId = (int)$_POST['quiz_id'];
    $questionText = sanitize($_POST['question_text']);
    $optA = sanitize($_POST['option_a']);
    $optB = sanitize($_POST['option_b']);
    $optC = sanitize($_POST['option_c']);
    $optD = sanitize($_POST['option_d']);
    $correct = strtoupper(sanitize($_POST['correct_option']));
    $explanation = sanitize($_POST['explanation']);

    $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quizId, $questionText, $optA, $optB, $optC, $optD, $correct, $explanation]);
    
    // Update quiz total_questions count
    $cntStmt = $pdo->prepare("UPDATE quizzes SET total_questions = (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = ?) WHERE id = ?");
    $cntStmt->execute([$quizId, $quizId]);

    $msg = "New question added to quiz successfully!";
}

// Bulk Import Quiz Questions Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_import_questions'])) {
    $quizId = (int)$_POST['quiz_id'];
    $importedCount = 0;
    $errors = [];

    // Process JSON or CSV File Upload
    if (isset($_FILES['question_file']) && $_FILES['question_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['question_file']['tmp_name'];
        $fileName = $_FILES['question_file']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($ext === 'json') {
            $content = file_get_contents($fileTmp);
            $items = json_decode($content, true);
            if (is_array($items)) {
                $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($items as $item) {
                    $qText = sanitize($item['question_text'] ?? $item['question'] ?? '');
                    $optA = sanitize($item['option_a'] ?? $item['a'] ?? '');
                    $optB = sanitize($item['option_b'] ?? $item['b'] ?? '');
                    $optC = sanitize($item['option_c'] ?? $item['c'] ?? '');
                    $optD = sanitize($item['option_d'] ?? $item['d'] ?? '');
                    $correct = strtoupper(sanitize($item['correct_option'] ?? $item['answer'] ?? 'A'));
                    $exp = sanitize($item['explanation'] ?? 'Correct answer verified.');

                    if (!empty($qText) && !empty($optA) && !empty($optB)) {
                        $stmt->execute([$quizId, $qText, $optA, $optB, $optC, $optD, $correct, $exp]);
                        $importedCount++;
                    }
                }
            } else {
                $errorMsg = "Invalid JSON structure for quiz questions.";
            }
        } elseif ($ext === 'csv' || $ext === 'txt') {
            $handle = fopen($fileTmp, 'r');
            $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
                if (count($row) >= 5 && strtolower(trim($row[0])) !== 'question' && strtolower(trim($row[0])) !== 'question_text') {
                    $qText = sanitize($row[0] ?? '');
                    $optA = sanitize($row[1] ?? '');
                    $optB = sanitize($row[2] ?? '');
                    $optC = sanitize($row[3] ?? '');
                    $optD = sanitize($row[4] ?? '');
                    $correct = strtoupper(sanitize($row[5] ?? 'A'));
                    $exp = sanitize($row[6] ?? 'Correct answer verified.');

                    if (!empty($qText) && !empty($optA)) {
                        $stmt->execute([$quizId, $qText, $optA, $optB, $optC, $optD, $correct, $exp]);
                        $importedCount++;
                    }
                }
            }
            fclose($handle);
        }
    }

    // Process Multi-line Text Area Input
    $textInput = trim($_POST['bulk_text_input'] ?? '');
    if (!empty($textInput)) {
        $lines = explode("\n", $textInput);
        $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) >= 5) {
                $qText = sanitize($parts[0]);
                $optA = sanitize($parts[1]);
                $optB = sanitize($parts[2]);
                $optC = sanitize($parts[3] ?? '');
                $optD = sanitize($parts[4] ?? '');
                $correct = strtoupper(sanitize($parts[5] ?? 'A'));
                $exp = sanitize($parts[6] ?? 'Correct answer verified.');

                $stmt->execute([$quizId, $qText, $optA, $optB, $optC, $optD, $correct, $exp]);
                $importedCount++;
            }
        }
    }

    if ($importedCount > 0) {
        $cntStmt = $pdo->prepare("UPDATE quizzes SET total_questions = (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = ?) WHERE id = ?");
        $cntStmt->execute([$quizId, $quizId]);

        $msg = "$importedCount quiz question(s) bulk imported successfully!";
    } else {
        $errorMsg = !empty($errors) ? implode('<br>', $errors) : "Please attach a JSON/CSV question file or paste multi-line question details.";
    }
}

// Fetch Quizzes and Questions
$quizzes = $pdo->query("
  SELECT q.*, c.title as course_title, (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as question_count 
  FROM quizzes q 
  JOIN courses c ON q.course_id = c.id 
  ORDER BY q.created_at ASC
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Quiz Builder & MCQ Manager</h3>
          <p class="text-muted small">Manage interactive quiz assessments and bulk import multiple-choice questions.</p>
        </div>
        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
          <button class="btn btn-outline-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#bulkQuestionModal">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Bulk Import Questions
          </button>
          <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="bi bi-plus-circle me-1"></i> Add Single Question
          </button>
        </div>
      </div>

      <?php if (!empty($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show small mb-4" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?php echo $msg; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $errorMsg; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <?php foreach ($quizzes as $qz): ?>
          <div class="col-lg-6">
            <div class="card-custom p-4 bg-white">
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2"><?php echo htmlspecialchars($qz['course_title']); ?></span>
              <h5 class="fw-bold font-heading text-dark mb-2"><?php echo htmlspecialchars($qz['title']); ?></h5>
              <p class="text-muted small mb-3"><?php echo htmlspecialchars($qz['description']); ?></p>

              <div class="d-flex align-items-center justify-content-between pt-3 border-top small text-muted">
                <span><i class="bi bi-clock me-1 text-primary"></i> Time: <strong><?php echo $qz['time_limit_mins']; ?> mins</strong></span>
                <span><i class="bi bi-question-circle me-1 text-primary"></i> Questions: <strong><?php echo $qz['question_count']; ?></strong></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</div>

<!-- Modal: Add Single Question -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="quizzes.php" method="POST">
        <input type="hidden" name="add_quiz_question" value="1">
        <div class="modal-header">
          <h5 class="modal-title font-heading fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Add Single Question</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Target Quiz</label>
            <select name="quiz_id" class="form-select" required>
              <?php foreach ($quizzes as $q): ?>
                <option value="<?php echo $q['id']; ?>"><?php echo htmlspecialchars($q['title']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Question Text</label>
            <textarea name="question_text" class="form-control" rows="2" required></textarea>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6"><input type="text" name="option_a" class="form-control" placeholder="Option A" required></div>
            <div class="col-md-6"><input type="text" name="option_b" class="form-control" placeholder="Option B" required></div>
            <div class="col-md-6"><input type="text" name="option_c" class="form-control" placeholder="Option C" required></div>
            <div class="col-md-6"><input type="text" name="option_d" class="form-control" placeholder="Option D" required></div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Correct Option</label>
            <select name="correct_option" class="form-select" required>
              <option value="A">Option A</option>
              <option value="B">Option B</option>
              <option value="C">Option C</option>
              <option value="D">Option D</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Explanation</label>
            <textarea name="explanation" class="form-control" rows="2" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-custom">Save Question</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Bulk Import Questions -->
<div class="modal fade" id="bulkQuestionModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="quizzes.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="bulk_import_questions" value="1">
        <div class="modal-header">
          <h5 class="modal-title font-heading fw-bold"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>Bulk Import Quiz Questions</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Target Quiz</label>
            <select name="quiz_id" class="form-select" required>
              <?php foreach ($quizzes as $q): ?>
                <option value="<?php echo $q['id']; ?>"><?php echo htmlspecialchars($q['title']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Upload JSON or CSV Question File (from device)</label>
            <input type="file" name="question_file" class="form-control" accept=".json,.csv,.txt">
            <div class="form-text text-muted small">Select a <code>.json</code> or <code>.csv</code> file containing multiple quiz questions.</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Or Paste Multiple Questions (Line-by-line)</label>
            <textarea name="bulk_text_input" class="form-control font-monospace small" rows="5" placeholder="Question | Option A | Option B | Option C | Option D | Correct (A/B/C/D) | Explanation&#10;e.g. What is 2+2? | 3 | 4 | 5 | 6 | B | Basic arithmetic addition."></textarea>
            <div class="form-text text-muted small">Format per line: <code>Question | Option A | Option B | Option C | Option D | CorrectOption | Explanation</code></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-custom"><i class="bi bi-upload me-1"></i> Import Questions</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
