/* Interactive Quiz System Script for ITS-BERT */

document.addEventListener('DOMContentLoaded', () => {
  const quizForm = document.getElementById('quizForm');
  const timerDisplay = document.getElementById('quizTimer');

  if (quizForm && timerDisplay) {
    let durationSeconds = parseInt(timerDisplay.getAttribute('data-seconds')) || 600;
    
    const interval = setInterval(() => {
      durationSeconds--;
      let mins = Math.floor(durationSeconds / 60);
      let secs = durationSeconds % 60;

      timerDisplay.innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

      if (durationSeconds <= 60) {
        timerDisplay.classList.add('bg-danger', 'text-white', 'animate-pulse');
      }

      if (durationSeconds <= 0) {
        clearInterval(interval);
        showToast("Time's up! Automatically submitting your quiz responses...", "warning");
        quizForm.submit();
      }
    }, 1000);
  }

  // Radio button selection highlight
  const optionLabels = document.querySelectorAll('.quiz-option-label');
  optionLabels.forEach(label => {
    label.addEventListener('click', () => {
      const name = label.querySelector('input').getAttribute('name');
      document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
        input.closest('.quiz-option-label').classList.remove('active', 'border-primary', 'bg-primary-subtle');
      });
      label.classList.add('active', 'border-primary', 'bg-primary-subtle');
    });
  });
});
