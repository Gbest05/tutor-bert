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
  <!-- Left Offcanvas Sidebar (History & Topics) -->
  <div class="tutor-sidebar" id="tutorSidebar">
    <div class="tutor-sidebar-header">
      <button class="btn btn-primary-custom w-100 mb-2 shadow-sm" id="clearBtn">
        <i class="bi bi-plus-lg me-1"></i> New Question
      </button>
      <div class="d-flex align-items-center justify-content-between">
        <small class="text-white-50 text-uppercase font-heading" style="font-size: 0.72rem; letter-spacing: 1px;">Recent Queries</small>
        <span class="badge bg-secondary-subtle text-light" style="font-size: 0.65rem;"><?php echo count($chatHistory); ?> Total</span>
      </div>
    </div>

    <div class="tutor-history-list">
      <?php if (empty($chatHistory)): ?>
        <div class="text-white-50 small p-3 text-center">No previous queries. Ask your first question below!</div>
      <?php else: ?>
        <?php foreach (array_reverse($chatHistory) as $item): ?>
          <div class="history-item text-truncate" onclick="document.getElementById('chatInput').value = '<?php echo addslashes($item['user_message']); ?>';">
            <i class="bi bi-chat-text text-info flex-shrink-0"></i>
            <span class="text-truncate" style="min-width: 0;"><?php echo htmlspecialchars($item['user_message']); ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="p-3 border-top border-secondary border-opacity-50 small">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-cpu-fill text-accent fs-5"></i>
        <div>
          <strong class="d-block text-white" style="font-size: 0.85rem;">BERT Transformer Engine</strong>
          <span class="text-info" style="font-size: 0.72rem;">Fine-Tuned for CS Studies</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Chat Interface Area -->
  <div class="tutor-main">
    <!-- Chat Header Bar -->
    <div class="tutor-chat-header">
      <div class="d-flex align-items-center gap-2">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn btn-sm btn-outline-secondary d-lg-none py-1 px-2 me-1" type="button" id="tutorSidebarToggle" title="View Recent Queries">
          <i class="bi bi-clock-history me-1"></i> <span class="d-none d-sm-inline">History</span>
        </button>
        
        <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle flex-shrink-0" width="38" height="38" style="object-fit: cover; border: 2px solid var(--accent-color);">
        <div style="min-width: 0;">
          <h6 class="mb-0 fw-bold font-heading text-truncate" style="font-size: 0.95rem;">BERT AI Tutor</h6>
          <small class="text-muted d-block text-truncate" style="font-size: 0.75rem;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 7px;"></i> Transformer NLP Active</small>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-md-inline-block py-2 px-3" style="font-size: 0.75rem;">
          <i class="bi bi-cpu me-1"></i> NLP REST API Active
        </span>
      </div>
    </div>

    <!-- Chat Box -->
    <div class="chat-box" id="chatBox">
      <!-- Default Initial Welcome Message -->
      <div class="chat-bubble bot">
        <div class="d-flex align-items-center gap-2 mb-1">
          <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="26" height="26" style="object-fit: cover;">
          <strong class="text-secondary font-heading" style="font-size: 0.88rem;">BERT AI Tutor</strong>
        </div>
        <p class="mb-0 text-dark" style="line-height: 1.5;">
          Hello <strong><?php echo htmlspecialchars($user['name']); ?></strong>! I am your intelligent tutoring assistant powered by BERT (Bidirectional Encoder Representations from Transformers). 
          What computer science topic or uploaded document would you like to learn about today?
        </p>
        <div class="mt-2 text-muted small"><i class="bi bi-lightbulb text-warning me-1"></i> Try asking: <em>"What is database normalization?"</em> or <em>"Explain encapsulation in Java"</em></div>
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
            <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="26" height="26" style="object-fit: cover;">
            <strong class="text-secondary font-heading" style="font-size: 0.88rem;">BERT AI Tutor</strong>
          </div>
          <div class="chat-content-text"><?php echo nl2br(htmlspecialchars($h['bot_response'])); ?></div>
          <span class="bert-badge mt-2"><i class="bi bi-cpu me-1"></i> BERT Confidence: <?php echo number_format($h['bert_confidence'], 2); ?>%</span>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Sticky Bottom Chat Input Bar -->
    <div class="chat-input-area">
      <form id="chatForm">
        <div class="chat-input-group">
          <button type="button" class="btn btn-link text-muted p-1 border-0" id="voiceBtn" title="Voice Input">
            <i class="bi bi-mic-fill fs-5"></i>
          </button>
          <input type="text" id="chatInput" placeholder="Ask BERT any question about Java, Data Structures, SQL, Web Security..." required autocomplete="off">
          <button type="submit" class="btn btn-primary-custom rounded-pill px-3 px-sm-4 py-2 flex-shrink-0">
            <i class="bi bi-send-fill me-1"></i> <span class="d-none d-sm-inline">Send</span>
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
