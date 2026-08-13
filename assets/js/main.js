/* Global App JS for ITS-BERT */

document.addEventListener('DOMContentLoaded', () => {
  // Animated Counters for Stats Section
  const counters = document.querySelectorAll('.counter-val');
  if (counters.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const target = +entry.target.getAttribute('data-target');
          let count = 0;
          const speed = target / 50;
          const updateCount = () => {
            count += speed;
            if (count < target) {
              entry.target.innerText = Math.ceil(count).toLocaleString();
              setTimeout(updateCount, 25);
            } else {
              entry.target.innerText = target.toLocaleString() + '+';
            }
          };
          updateCount();
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
  }

  // Password Visibility Toggle
  const togglePassBtns = document.querySelectorAll('.toggle-password');
  togglePassBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling || document.querySelector(btn.getAttribute('data-target'));
      if (input) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        btn.querySelector('i').classList.toggle('bi-eye');
        btn.querySelector('i').classList.toggle('bi-eye-slash');
      }
    });
  });

  // Mobile Hamburger Toggle Handler
  const mobileNavBtn = document.getElementById('mobilePortalNavToggle');
  const dashSidebar = document.querySelector('.dash-sidebar');
  const mobilePortalNav = document.getElementById('mobilePortalNav');

  if (mobileNavBtn) {
    mobileNavBtn.addEventListener('click', (e) => {
      e.preventDefault();

      // 1. Toggle dashboard offcanvas sidebar drawer on mobile if present
      if (dashSidebar && window.innerWidth <= 991) {
        let backdrop = document.querySelector('.sidebar-backdrop');
        if (!backdrop) {
          backdrop = document.createElement('div');
          backdrop.className = 'sidebar-backdrop';
          document.body.appendChild(backdrop);
        }
        dashSidebar.classList.toggle('show');
        backdrop.classList.toggle('show');

        backdrop.onclick = () => {
          dashSidebar.classList.remove('show');
          backdrop.classList.remove('show');
        };
      }

      // 2. Toggle Bootstrap collapse menu if present
      if (mobilePortalNav && typeof bootstrap !== 'undefined') {
        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(mobilePortalNav);
        bsCollapse.toggle();
      }
    });
  }

  if (dashSidebar) {
    const menuLinks = dashSidebar.querySelectorAll('a');
    menuLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 991) {
          dashSidebar.classList.remove('show');
          const backdrop = document.querySelector('.sidebar-backdrop');
          if (backdrop) backdrop.classList.remove('show');
        }
      });
    });
  }
});

/**
 * Custom Toast Alert Trigger
 */
function showToast(message, type = 'success') {
  const toastContainer = document.getElementById('toastContainer') || createToastContainer();
  const bgClass = type === 'success' ? 'bg-success' : (type === 'danger' ? 'bg-danger' : 'bg-primary');
  
  const toastHtml = `
    <div class="toast align-items-center text-white ${bgClass} border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body font-main fw-medium">
          <i class="bi bi-info-circle-fill me-2"></i> ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;
  toastContainer.insertAdjacentHTML('beforeend', toastHtml);
  setTimeout(() => {
    const el = toastContainer.querySelector('.toast');
    if (el) el.remove();
  }, 4000);
}

function createToastContainer() {
  const container = document.createElement('div');
  container.id = 'toastContainer';
  container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
  container.style.zIndex = '9999';
  document.body.appendChild(container);
  return container;
}
