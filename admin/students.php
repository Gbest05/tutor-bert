<?php
$page_title = "Manage Students";
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

require_admin();

// Delete Student Action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $userId = (int)($_GET['user_id'] ?? 0);
    if ($userId > 0) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        $stmt->execute([$userId]);
        header("Location: students.php?msg=deleted");
        exit();
    }
}

// Fetch All Students
$students = $pdo->query("
  SELECT s.*, u.id as user_id, u.name, u.email, u.created_at as reg_date 
  FROM students s 
  JOIN users u ON s.user_id = u.id 
  ORDER BY s.created_at DESC
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
          <h3 class="fw-bold font-heading text-dark mb-1">Student Records & Management</h3>
          <p class="text-muted small">View, search, edit, and manage registered students.</p>
        </div>
      </div>

      <div class="card-custom p-4 bg-white">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light small">
              <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Student Code</th>
                <th>Department</th>
                <th>Phone</th>
                <th>Registered Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="small">
              <?php foreach ($students as $st): ?>
                <tr>
                  <td>#<?php echo $st['id']; ?></td>
                  <td class="fw-bold text-dark"><?php echo htmlspecialchars($st['name']); ?></td>
                  <td><?php echo htmlspecialchars($st['email']); ?></td>
                  <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?php echo htmlspecialchars($st['student_id_code']); ?></span></td>
                  <td><?php echo htmlspecialchars($st['department']); ?></td>
                  <td><?php echo htmlspecialchars($st['phone'] ?? 'N/A'); ?></td>
                  <td class="text-muted"><?php echo date('M d, Y', strtotime($st['reg_date'])); ?></td>
                  <td>
                    <a href="students.php?action=delete&user_id=<?php echo $st['user_id']; ?>" onclick="return confirm('Delete this student account?');" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                  </td>
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
