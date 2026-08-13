<?php
$is_admin_dir = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
$base_path = $is_admin_dir ? '../' : '';
?>
  <!-- Footer -->
  <footer class="bg-dark text-white pt-5 pb-4 mt-auto border-top border-secondary border-opacity-25">
    <div class="container">
      <div class="row g-4 justify-content-between">
        <div class="col-lg-5 col-md-6">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-cpu-fill text-info fs-3"></i>
            <span class="fs-4 fw-bold text-white font-heading">ITS-BERT</span>
          </div>
          <p class="text-secondary small">
            An Intelligent Tutoring System powered by Bidirectional Encoder Representations from Transformers (BERT) to deliver smart, personalized Computer Science education.
          </p>
          <div class="d-flex gap-3 text-info fs-5 mt-3">
            <a href="#" class="text-secondary"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-secondary"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="text-secondary"><i class="bi bi-linkedin"></i></a>
            <a href="#" class="text-secondary"><i class="bi bi-github"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="text-white fw-bold mb-3 font-heading">Quick Links</h6>
          <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary">
            <li><a href="<?php echo $base_path; ?>index.php" class="text-secondary text-decoration-none">Home</a></li>
            <li><a href="<?php echo $base_path; ?>courses.php" class="text-secondary text-decoration-none">Courses Catalog</a></li>
            <li><a href="<?php echo $base_path; ?>materials.php" class="text-secondary text-decoration-none">Resource Library</a></li>
            <li><a href="<?php echo $base_path; ?>ai_tutor.php" class="text-secondary text-decoration-none">BERT AI Tutor</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-6">
          <h6 class="text-white fw-bold mb-3 font-heading">Core Features</h6>
          <ul class="list-unstyled small d-flex flex-column gap-2 text-secondary">
            <li><i class="bi bi-check-circle me-1 text-info"></i> Natural Language Processing</li>
            <li><i class="bi bi-check-circle me-1 text-info"></i> Contextual Answer Retrieval</li>
            <li><i class="bi bi-check-circle me-1 text-info"></i> Interactive MCQ Quizzes</li>
            <li><i class="bi bi-check-circle me-1 text-info"></i> Real-time Analytics</li>
          </ul>
        </div>
      </div>

      <hr class="my-4 border-secondary opacity-25">

      <div class="text-center small text-secondary">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> ITS-BERT | Intelligent Tutoring System. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Chart.js CDN for Analytics -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Global Custom App Script -->
  <script src="<?php echo $base_path; ?>assets/js/main.js"></script>
</body>
</html>
