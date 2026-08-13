<?php
$page_title = "BERT AI Tutor - ChatGPT Interface";
require_once 'config/db.php';
require_once 'config/auth.php';

require_student();

$user = get_logged_user();
$studentId = $user['student_id'] ?? 1;

// Fetch Chat History
$chatHistory = [];
if (isset($pdo) && $pdo !== null) {
    try {
        $hStmt = $pdo->prepare("SELECT * FROM chatbot_history WHERE student_id = ? ORDER BY created_at ASC");
        $hStmt->execute([$studentId]);
        $chatHistory = $hStmt->fetchAll();
    } catch (Exception $e) {
        $chatHistory = [];
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<div class="tutor-container">
  <!-- Left Sidebar (History & Topics) -->
  <div class="tutor-sidebar d-none d-lg-flex" id="tutorSidebar">
    <div class="tutor-sidebar-header">
      <button class="btn btn-primary-custom w-100 mb-2" id="clearBtn">
        <i class="bi bi-plus-lg me-1"></i> New Question
      </button>
      <small class="text-white-50 text-uppercase font-heading" style="font-size: 0.75rem; letter-spacing: 1px;">Recent Queries</small>
    </div>

    <div class="tutor-history-list">
      <?php if (empty($chatHistory)): ?>
        <div class="text-white-50 small p-3 text-center">No previous queries. Ask your first question!</div>
      <?php else: ?>
        <?php foreach (array_reverse($chatHistory) as $item): ?>
          <div class="history-item text-truncate" onclick="document.getElementById('chatInput').value = '<?php echo addslashes($item['user_message']); ?>';">
            <i class="bi bi-chat-text text-info"></i>
            <span class="text-truncate"><?php echo htmlspecialchars($item['user_message']); ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="p-3 border-top border-secondary opacity-75 small">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-cpu-fill text-accent fs-5"></i>
        <div>
          <strong class="d-block text-white">BERT-Base Engine</strong>
          <span class="text-info" style="font-size: 0.75rem;">Fine-Tuned for CS</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Chat Interface Area -->
  <div class="tutor-main">
    <div class="tutor-chat-header">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-light border d-lg-none" type="button" onclick="document.getElementById('tutorSidebar').classList.toggle('d-none');">
          <i class="bi bi-layout-sidebar"></i>
        </button>
        <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="40" height="40" style="object-fit: cover; border: 2px solid var(--accent-color);">
        <div>
          <h6 class="mb-0 fw-bold font-heading">BERT AI Tutor</h6>
          <small class="text-muted"><i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> Powered by Transformer NLP Engine</small>
        </div>
      </div>

      <div class="d-flex gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-md-inline-block py-2 px-3">
          <i class="bi bi-cpu me-1"></i> NLP REST API Active
        </span>
      </div>
    </div>

    <!-- Chat Box -->
    <div class="chat-box" id="chatBox">
      <!-- Default Initial Welcome Message -->
      <div class="chat-bubble bot">
        <div class="d-flex align-items-center gap-2 mb-1">
          <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="28" height="28" style="object-fit: cover;">
          <strong class="text-secondary font-heading" style="font-size: 0.9rem;">BERT AI Tutor</strong>
        </div>
        <p class="mb-0 text-dark">
          Hello <strong><?php echo htmlspecialchars($user['name']); ?></strong>! I am your intelligent tutoring assistant powered by BERT (Bidirectional Encoder Representations from Transformers). 
          What computer science topic would you like to learn today?
        </p>
        <div class="mt-2 text-muted small"><i class="bi bi-lightbulb text-warning me-1"></i> Try asking: <em>"What is database normalization?"</em> or <em>"Explain encapsulation in C++"</em></div>
      </div>

      <!-- Populate Existing Chat History -->
      <?php foreach ($chatHistory as $h): ?>
        <div class="chat-bubble student">
          <div class="d-flex align-items-center justify-content-end gap-2 mb-1 text-white-50 small">
            <span>You</span>
            <i class="bi bi-person-circle"></i>
          </div>
          <p class="mb-0"><?php echo htmlspecialchars($h['user_message']); ?></p>
        </div>

        <div class="chat-bubble bot">
          <div class="d-flex align-items-center gap-2 mb-1">
            <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="28" height="28" style="object-fit: cover;">
            <strong class="text-secondary font-heading" style="font-size: 0.9rem;">BERT AI Tutor</strong>
          </div>
          <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($h['bot_response'])); ?></p>
          <span class="bert-badge"><i class="bi bi-cpu"></i> BERT Confidence: <?php echo number_format($h['bert_confidence'], 2); ?>%</span>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Chat Input Area -->
    <div class="chat-input-area">
      <form id="chatForm">
        <div class="chat-input-group">
          <button type="button" class="btn btn-link text-muted" id="voiceBtn" title="Voice Input">
            <i class="bi bi-mic-fill fs-5"></i>
          </button>
          <input type="text" id="chatInput" placeholder="Ask BERT any question about OOP, DBMS, Data Structures, Web Security..." required autocomplete="off">
          <button type="submit" class="btn btn-primary-custom rounded-pill px-4">
            <i class="bi bi-send-fill me-1"></i> Send
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  window.BASE_PATH = '<?php echo isset($base_path) ? $base_path : ''; ?>';
</script>
<script src="assets/js/ai_tutor.js?v=<?php echo time(); ?>"></script>

<?php require_once 'includes/portal_footer.php'; ?>
