<?php
$page_title = "Course Catalog & Management";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$user = get_logged_user();
$studentId = $user['student_id'] ?? 1;

$stmt = $pdo->prepare("
  SELECT c.*, COALESCE(p.completed_lessons, 0) as completed_lessons 
  FROM courses c 
  LEFT JOIN progress p ON c.id = p.course_id AND p.student_id = ?
  ORDER BY c.created_at ASC
");
$stmt->execute([$studentId]);
$courses = $stmt->fetchAll();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="dashboard-layout">
  <?php require_once 'includes/sidebar.php'; ?>

  <main class="dash-main">
    <div class="container-fluid p-0">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
          <h3 class="fw-bold font-heading text-dark mb-1">Computer Science Courses</h3>
          <p class="text-muted small">Explore modules, lesson content, and associated interactive quizzes.</p>
        </div>
      </div>

      <div class="row g-4">
        <?php foreach ($courses as $c): 
          $pct = $c['total_lessons'] > 0 ? round(($c['completed_lessons'] / $c['total_lessons']) * 100) : 0;
        ?>
          <div class="col-lg-4 col-md-6">
            <div class="card-custom h-100 bg-white">
              <img src="assets/images/<?php echo htmlspecialchars($c['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($c['title']); ?>" style="height: 190px; object-fit: cover;">
              
              <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?php echo htmlspecialchars($c['category']); ?></span>
                  <small class="text-muted"><i class="bi bi-person-fill text-secondary me-1"></i> <?php echo htmlspecialchars($c['instructor']); ?></small>
                </div>

                <h5 class="fw-bold font-heading text-dark mb-2"><?php echo htmlspecialchars($c['title']); ?></h5>
                <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars($c['description']); ?></p>

                <div class="my-3">
                  <div class="d-flex justify-content-between align-items-center small mb-1">
                    <span class="text-muted">Progress</span>
                    <span class="fw-bold text-primary"><?php echo $pct; ?>%</span>
                  </div>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $pct; ?>%;"></div>
                  </div>
                </div>

                <a href="course_view.php?id=<?php echo $c['id']; ?>" class="btn btn-primary-custom w-100 mt-2">
                  <i class="bi bi-play-circle-fill me-1"></i> <?php echo $pct > 0 ? 'Continue Course' : 'Start Course'; ?>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</div>

<?php require_once 'includes/portal_footer.php'; ?>
