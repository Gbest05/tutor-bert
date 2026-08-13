<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$studentUser = get_logged_user();
$studentAvatar = !empty($studentUser['avatar']) ? 'assets/images/' . htmlspecialchars($studentUser['avatar']) : 'assets/images/student_avatar.jpg';
$site_settings = get_site_settings();
?>
<aside class="dash-sidebar" style="width: 260px;">
  <div>
    <!-- Fixed Top Student Profile Header -->
    <div class="dash-sidebar-header">
      <!-- Sidebar Brand Branding -->
      <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-25">
        <?php if (!empty($site_settings['logo_image'])): ?>
          <img src="assets/images/<?php echo htmlspecialchars($site_settings['logo_image']); ?>" height="32" class="rounded me-1" alt="Logo">
        <?php else: ?>
          <div class="portal-brand-icon" style="width: 32px; height: 32px; font-size: 1rem;">
            <i class="bi <?php echo htmlspecialchars($site_settings['logo_icon'] ?? 'bi-cpu-fill'); ?>"></i>
          </div>
        <?php endif; ?>
        <div>
          <span class="fw-bold text-white font-heading d-block leading-none" style="font-size: 1.05rem; letter-spacing: -0.3px;"><?php echo htmlspecialchars($site_settings['site_name']); ?></span>
          <small style="font-size: 0.65rem; color: #38bdf8;"><?php echo htmlspecialchars($site_settings['site_subtitle']); ?></small>
        </div>
      </div>

      <!-- Student Profile Badge -->
      <div class="d-flex align-items-center gap-2 px-1">
        <img src="<?php echo $studentAvatar; ?>" alt="Student Avatar" class="rounded-circle flex-shrink-0" width="40" height="40" style="object-fit: cover; border: 2px solid var(--accent-color);">
        <div style="min-width: 0;">
          <h6 class="text-white mb-0 font-heading text-truncate" style="font-size: 0.9rem;"><?php echo htmlspecialchars($studentUser['name'] ?? 'Student'); ?></h6>
          <small class="text-info" style="font-size: 0.75rem;">ND II Computer Science</small>
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
          <i class="bi bi-file-earmark-check-fill"></i> Quizzes & Assessments
        </a>
      </li>
      <li>
        <a href="progress.php" class="<?php echo ($currentPage == 'progress.php') ? 'active' : ''; ?>">
          <i class="bi bi-graph-up-arrow"></i> My Progress
        </a>
      </li>
      <li>
        <a href="profile.php" class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
          <i class="bi bi-person-gear"></i> Profile & Settings
        </a>
      </li>
    </ul>
  </div>

  <!-- Logout Footer -->
  <div class="dash-sidebar-bottom">
    <a href="logout.php" class="btn btn-outline-danger btn-sm w-100 font-heading fw-semibold">
      <i class="bi bi-box-arrow-right me-1"></i> Logout Account
    </a>
  </div>
</aside>
