<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$studentUser = get_logged_user();
$studentAvatar = !empty($studentUser['avatar']) ? 'assets/images/' . htmlspecialchars($studentUser['avatar']) : 'assets/images/student_avatar.jpg';
?>
<aside class="dash-sidebar" style="width: 260px;">
  <div>
    <!-- Fixed Top Student Profile Header (Unpadded & Fixed at top) -->
    <div class="dash-sidebar-header">
      <div class="d-flex align-items-center gap-2 px-1">
        <img src="<?php echo $studentAvatar; ?>" alt="Student Avatar" class="rounded-circle flex-shrink-0" width="42" height="42" style="object-fit: cover; border: 2px solid var(--accent-color);">
        <div style="min-width: 0;">
          <h6 class="text-white mb-0 font-heading text-truncate" style="font-size: 0.92rem;"><?php echo htmlspecialchars($studentUser['name'] ?? 'Student'); ?></h6>
          <small class="text-info" style="font-size: 0.78rem;">ND II Computer Science</small>
        </div>
      </div>
    </div>

    <!-- Student Navigation Menu -->
    <ul class="dash-menu">
      <li>
        <a href="dashboard.php" class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
          <i class="bi bi-grid-fill"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="ai_tutor.php" class="<?php echo ($currentPage == 'ai_tutor.php') ? 'active' : ''; ?>">
          <i class="bi bi-robot"></i> AI Tutor (BERT)
        </a>
      </li>
      <li>
        <a href="courses.php" class="<?php echo ($currentPage == 'courses.php' || $currentPage == 'course_view.php') ? 'active' : ''; ?>">
          <i class="bi bi-journal-bookmark-fill"></i> My Courses
        </a>
      </li>
      <li>
        <a href="materials.php" class="<?php echo ($currentPage == 'materials.php') ? 'active' : ''; ?>">
          <i class="bi bi-folder-symlink-fill"></i> Learning Materials
        </a>
      </li>
      <li>
        <a href="quizzes.php" class="<?php echo ($currentPage == 'quizzes.php' || $currentPage == 'take_quiz.php') ? 'active' : ''; ?>">
          <i class="bi bi-file-earmark-check-fill"></i> Quizzes
        </a>
      </li>
      <li>
        <a href="progress.php" class="<?php echo ($currentPage == 'progress.php') ? 'active' : ''; ?>">
          <i class="bi bi-graph-up-arrow"></i> Progress Tracking
        </a>
      </li>
      <li>
        <a href="profile.php" class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
          <i class="bi bi-person-gear"></i> Profile & Settings
        </a>
      </li>
    </ul>
  </div>

  <!-- Signout Button at bottom with top margin -->
  <div class="dash-sidebar-bottom">
    <a href="logout.php" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold">
      <i class="bi bi-box-arrow-right"></i> Sign Out
    </a>
  </div>
</aside>
