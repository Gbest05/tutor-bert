<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$adminUser = get_logged_user();
$adminAvatar = !empty($adminUser['avatar']) ? '../assets/images/' . htmlspecialchars($adminUser['avatar']) : '../assets/images/ai_tutor_avatar.jpg';
$site_settings = get_site_settings();
?>
<aside class="dash-sidebar" style="width: 260px;">
  <div>
    <!-- Fixed Top Admin Profile Header -->
    <div class="dash-sidebar-header">
      <!-- Sidebar Brand Branding -->
      <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-25">
        <?php if (!empty($site_settings['logo_image'])): ?>
          <img src="../assets/images/<?php echo htmlspecialchars($site_settings['logo_image']); ?>" height="32" class="rounded me-1" alt="Logo">
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

      <!-- Clean User Profile Badge -->
      <div class="d-flex align-items-center gap-2 px-1 py-1">
        <img src="<?php echo $adminAvatar; ?>" alt="User Avatar" class="rounded-circle flex-shrink-0" width="40" height="40" style="object-fit: cover; border: 2px solid var(--accent-color);">
        <div style="min-width: 0;">
          <h6 class="text-white mb-0 font-heading text-truncate" style="font-size: 0.9rem;"><?php echo htmlspecialchars($adminUser['name'] ?? 'User'); ?></h6>
        </div>
      </div>
    </div>

    <!-- Admin Navigation Menu -->
    <ul class="dash-menu">
      <li>
        <a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
          <i class="bi bi-speedometer2"></i> Overview
        </a>
      </li>
      <li>
        <a href="students.php" class="<?php echo ($currentPage == 'students.php') ? 'active' : ''; ?>">
          <i class="bi bi-people-fill"></i> Manage Students
        </a>
      </li>
      <li>
        <a href="courses.php" class="<?php echo ($currentPage == 'courses.php') ? 'active' : ''; ?>">
          <i class="bi bi-journal-album"></i> Courses & Lessons
        </a>
      </li>
      <li>
        <a href="quizzes.php" class="<?php echo ($currentPage == 'quizzes.php') ? 'active' : ''; ?>">
          <i class="bi bi-file-earmark-check-fill"></i> Quizzes & Exams
        </a>
      </li>
      <li>
        <a href="materials.php" class="<?php echo ($currentPage == 'materials.php') ? 'active' : ''; ?>">
          <i class="bi bi-folder-fill"></i> Resource Files
        </a>
      </li>
      <li>
        <a href="questions.php" class="<?php echo ($currentPage == 'questions.php') ? 'active' : ''; ?>">
          <i class="bi bi-chat-left-dots-fill"></i> Question Audit Logs
        </a>
      </li>
      <li>
        <a href="settings.php" class="<?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>">
          <i class="bi bi-gear-fill"></i> Site Configuration
        </a>
      </li>
      <li>
        <a href="profile.php" class="<?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
          <i class="bi bi-person-gear"></i> Admin Security
        </a>
      </li>
    </ul>
  </div>

  <!-- Logout Footer -->
  <div class="dash-sidebar-bottom">
    <a href="../logout.php" class="btn btn-outline-danger btn-sm w-100 font-heading fw-semibold">
      <i class="bi bi-box-arrow-right me-1"></i> Exit Portal
    </a>
  </div>
</aside>
