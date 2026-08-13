<?php
$page_title = "Course Viewer";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$courseId = (int)($_GET['id'] ?? 1);

// Fetch Course Details
$cStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$cStmt->execute([$courseId]);
$course = $cStmt->fetch();

if (!$course) {
    header("Location: courses.php");
    exit();
}

// Fetch Lessons
$lStmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_num ASC");
$lStmt->execute([$courseId]);
$lessons = $lStmt->fetchAll();

// Fetch Course Materials
$mStmt = $pdo->prepare("SELECT * FROM learning_materials WHERE course_id = ?");
$mStmt->execute([$courseId]);
$materials = $mStmt->fetchAll();

// Fetch Related Quiz
$qStmt = $pdo->prepare("SELECT id, title FROM quizzes WHERE course_id = ? LIMIT 1");
$qStmt->execute([$courseId]);
$quiz = $qStmt->fetch();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once 'includes/sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <!-- Course Banner -->
      <div class="card-custom p-4 bg-white mb-4">
        <div class="row align-items-center g-4">
          <div class="col-md-3">
            <img src="assets/images/<?php echo htmlspecialchars($course['image']); ?>" class="img-fluid rounded-3 shadow-sm w-100" style="height: 140px; object-fit: cover;">
          </div>
          <div class="col-md-9">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2"><?php echo htmlspecialchars($course['category']); ?></span>
            <h3 class="fw-bold font-heading mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
            <p class="text-muted small mb-3"><?php echo htmlspecialchars($course['description']); ?></p>
            <div class="d-flex flex-wrap gap-3 align-items-center">
              <span class="small text-muted"><i class="bi bi-person-fill text-primary me-1"></i> Instructor: <strong><?php echo htmlspecialchars($course['instructor']); ?></strong></span>
              <span class="small text-muted"><i class="bi bi-journal-text text-primary me-1"></i> Lessons: <strong><?php echo count($lessons); ?></strong></span>
              <?php if ($quiz): ?>
                <a href="take_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-accent-custom btn-sm ms-auto">
                  <i class="bi bi-file-earmark-check-fill me-1"></i> Take Course Quiz
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Lessons Accordion -->
        <div class="col-lg-8">
          <div class="card-custom p-4 bg-white">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-list-task text-primary me-2"></i> Course Syllabus & Lessons</h5>

            <div class="accordion" id="lessonsAccordion">
              <?php foreach ($lessons as $index => $lesson): ?>
                <div class="accordion-item mb-2 border rounded-3 overflow-hidden">
                  <h2 class="accordion-header" id="heading<?php echo $lesson['id']; ?>">
                    <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?> font-heading fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $lesson['id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $lesson['id']; ?>">
                      <span class="badge bg-primary rounded-circle me-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;"><?php echo $lesson['order_num']; ?></span>
                      <?php echo htmlspecialchars($lesson['title']); ?>
                    </button>
                  </h2>
                  <div id="collapse<?php echo $lesson['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $lesson['id']; ?>" data-bs-parent="#lessonsAccordion">
                    <div class="accordion-body text-dark small leading-relaxed p-4 bg-light">
                      <?php echo nl2br(htmlspecialchars($lesson['content'])); ?>
                      <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <a href="ai_tutor.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-robot me-1"></i> Ask BERT about this lesson</a>
                        <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Lesson Complete</span>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Course Resources Sidebar -->
        <div class="col-lg-4">
          <div class="card-custom p-4 bg-white mb-4">
            <h5 class="fw-bold font-heading mb-3"><i class="bi bi-folder-symlink text-primary me-2"></i> Course Files & Notes</h5>
            <?php if (empty($materials)): ?>
              <p class="text-muted small mb-0">No lecture files uploaded for this course yet.</p>
            <?php else: ?>
              <div class="d-flex flex-column gap-2">
                <?php foreach ($materials as $m): ?>
                  <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                      <i class="bi bi-file-earmark-pdf-fill fs-4 text-danger"></i>
                      <div>
                        <strong class="d-block small text-dark"><?php echo htmlspecialchars($m['title']); ?></strong>
                        <small class="text-muted"><?php echo strtoupper($m['type']); ?> • <?php echo $m['downloads']; ?> downloads</small>
                      </div>
                    </div>
                    <a href="materials.php" class="btn btn-sm btn-light border"><i class="bi bi-download"></i></a>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="card-custom p-4 bg-primary text-white">
            <h6 class="fw-bold font-heading mb-2"><i class="bi bi-cpu-fill text-accent me-1"></i> Need Help with this Module?</h6>
            <p class="small text-white-50 mb-3">BERT is available 24/7 to clarify concepts, answer questions, or provide additional coding examples.</p>
            <a href="ai_tutor.php" class="btn btn-light text-primary btn-sm fw-bold w-100"><i class="bi bi-chat-text-fill me-1"></i> Chat with BERT Tutor</a>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once 'includes/portal_footer.php'; ?>
