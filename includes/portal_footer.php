<?php
$is_admin_dir = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
$base_path = $is_admin_dir ? '../' : '';
?>
  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Chart.js CDN for Analytics -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Global Custom App Script -->
  <script src="<?php echo $base_path; ?>assets/js/main.js"></script>
</body>
</html>
