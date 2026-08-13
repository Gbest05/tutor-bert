/* AI Tutor ChatGPT-Style Script for ITS-BERT */

document.addEventListener('DOMContentLoaded', () => {
  const chatForm = document.getElementById('chatForm');
  const chatInput = document.getElementById('chatInput');
  const chatBox = document.getElementById('chatBox');
  const voiceBtn = document.getElementById('voiceBtn');
  const clearBtn = document.getElementById('clearBtn');

  if (chatForm && chatInput && chatBox) {
    chatForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const question = chatInput.value.trim();
      if (!question) return;

      // Append Student Message
      appendMessage(question, 'student');
      chatInput.value = '';

      // Show BERT Processing Indicator Loader
      const loaderId = showBertLoader();

      try {
        const basePath = typeof window.BASE_PATH !== 'undefined' ? window.BASE_PATH : '';
        const apiUrl = basePath + 'ai_chat_endpoint.php';
        
        // Use standard form encoding to bypass InfinityFree application/json 403 WAF rule
        const bodyParams = new URLSearchParams();
        bodyParams.append('question', question);

        let response = await fetch(apiUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: bodyParams
        });

        // Fallback retry using FormData if primary endpoint receives 403
        if (response.status === 403) {
          const formData = new FormData();
          formData.append('question', question);
          response = await fetch(basePath + 'api/ai_chat.php', {
            method: 'POST',
            body: formData
          });
        }

        const rawText = await response.text();
        let data = null;
        try {
          const jsonMatch = rawText.match(/\{[\s\S]*\}/);
          data = jsonMatch ? JSON.parse(jsonMatch[0]) : JSON.parse(rawText);
        } catch (parseErr) {
          console.error("Server raw response error:", rawText);
        }

        removeBertLoader(loaderId);

        if (data && data.success) {
          appendMessage(data.response, 'bot', data.bert_confidence, data.recommended_course, data.history_id);
        } else if (data && data.error) {
          appendMessage("⚠️ BERT AI Notice: " + data.error, 'bot');
        } else {
          // If server returned non-JSON HTML error page, extract clean text message
          let errorText = "Unable to process AI response.";
          if (rawText) {
            const tempEl = document.createElement('div');
            tempEl.innerHTML = rawText;
            errorText = (tempEl.textContent || tempEl.innerText || rawText).trim().substring(0, 250);
          }
          appendMessage("⚠️ Server Notice: " + errorText, 'bot');
        }
      } catch (err) {
        removeBertLoader(loaderId);
        appendMessage("⚠️ Connection Issue: Unable to connect to the server endpoint. Please verify server connectivity or database setup in config/db.php.", 'bot');
      }
    });

    // Speech-to-Text Input Handler
    if (voiceBtn && ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
      const recognition = new SpeechRecognition();
      recognition.continuous = false;
      recognition.interimResults = false;
      recognition.lang = 'en-US';

      voiceBtn.addEventListener('click', () => {
        voiceBtn.classList.add('text-danger', 'animate-pulse');
        recognition.start();
        showToast("Voice listening activated... Speak your question now.", "info");
      });

      recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        chatInput.value = transcript;
        voiceBtn.classList.remove('text-danger', 'animate-pulse');
      };

      recognition.onerror = () => {
        voiceBtn.classList.remove('text-danger', 'animate-pulse');
        showToast("Voice recognition error or cancelled.", "danger");
      };

      recognition.onend = () => {
        voiceBtn.classList.remove('text-danger', 'animate-pulse');
      };
    } else if (voiceBtn) {
      voiceBtn.addEventListener('click', () => {
        showToast("Speech recognition is not supported in this browser. Please type your query.", "warning");
      });
    }

    // Clear Conversation
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        if (confirm("Clear current conversation window?")) {
          chatBox.innerHTML = `
            <div class="chat-bubble bot">
              <div class="d-flex align-items-center gap-2 mb-1">
                <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="28" height="28">
                <strong>BERT AI Tutor</strong>
              </div>
              <p class="mb-0">Conversation cleared! What else would you like to learn today?</p>
            </div>
          `;
        }
      });
    }
  }
});

function appendMessage(text, sender, confidence = null, course = null, historyId = null) {
  const chatBox = document.getElementById('chatBox');
  const bubble = document.createElement('div');
  bubble.className = `chat-bubble ${sender}`;

  if (sender === 'student') {
    bubble.innerHTML = `
      <div class="d-flex align-items-center justify-content-end gap-2 mb-1 text-white-50 small">
        <span>You</span>
        <i class="bi bi-person-circle"></i>
      </div>
      <p class="mb-0">${escapeHtml(text)}</p>
    `;
  } else {
    let courseBadge = course ? `<div class="mt-2 text-muted small"><i class="bi bi-book me-1 text-primary"></i> <strong>Recommended:</strong> ${escapeHtml(course)}</div>` : '';
    let confBadge = confidence ? `<span class="bert-badge"><i class="bi bi-cpu"></i> BERT Confidence: ${confidence}%</span>` : '';
    let actions = historyId ? `
      <div class="d-flex gap-2 mt-2 pt-2 border-top border-light-subtle text-muted small">
        <button class="btn btn-sm btn-link text-decoration-none text-muted p-0 me-2" onclick="copyText('${escapeHtml(text)}')"><i class="bi bi-copy"></i> Copy</button>
        <button class="btn btn-sm btn-link text-decoration-none text-muted p-0 me-2" onclick="feedback(${historyId}, 'like', this)"><i class="bi bi-hand-thumbs-up"></i> Helpful</button>
        <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" onclick="feedback(${historyId}, 'dislike', this)"><i class="bi bi-hand-thumbs-down"></i> Not Helpful</button>
      </div>
    ` : '';

    bubble.innerHTML = `
      <div class="d-flex align-items-center gap-2 mb-1">
        <img src="assets/images/ai_tutor_avatar.jpg" class="rounded-circle" width="28" height="28" style="object-fit: cover;">
        <strong class="text-secondary font-heading" style="font-size: 0.9rem;">BERT AI Tutor</strong>
      </div>
      <p class="mb-0 text-dark">${formatMarkdown(text)}</p>
      ${confBadge}
      ${courseBadge}
      ${actions}
    `;
  }

  chatBox.appendChild(bubble);
  chatBox.scrollTop = chatBox.scrollHeight;
}

function showBertLoader() {
  const chatBox = document.getElementById('chatBox');
  const loader = document.createElement('div');
  const id = 'loader-' + Date.now();
  loader.id = id;
  loader.className = 'chat-bubble bot';
  loader.innerHTML = `
    <div class="d-flex align-items-center gap-2">
      <div class="typing-dots"><span></span><span></span><span></span></div>
      <span class="text-primary font-heading fw-semibold small">BERT is analyzing your question...</span>
    </div>
  `;
  chatBox.appendChild(loader);
  chatBox.scrollTop = chatBox.scrollHeight;
  return id;
}

function removeBertLoader(id) {
  const loader = document.getElementById(id);
  if (loader) loader.remove();
}

function copyText(text) {
  navigator.clipboard.writeText(text);
  showToast("Response copied to clipboard!", "success");
}

async function feedback(historyId, type, btn) {
  try {
    const res = await fetch('api/ai_chat.php?action=feedback', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ history_id: historyId, feedback: type })
    });
    if (res.ok) {
      btn.parentNode.querySelectorAll('button').forEach(b => b.classList.add('disabled'));
      btn.classList.add('text-primary', 'fw-bold');
      showToast("Thank you for your feedback!", "success");
    }
  } catch(e) {}
}

function escapeHtml(str) {
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

function formatMarkdown(str) {
  return escapeHtml(str).replace(/\n/g, '<br>');
}
