const nav = document.getElementById('mainNav');
if (nav) {
  const isLocked = nav.getAttribute('data-locked') === 'true';
  if (!isLocked) {
    window.addEventListener('scroll', () => {
      nav.classList.toggle('scrolled', window.scrollY > 40);
    });
  } else {
    nav.classList.add('scrolled');
  }
}

  // Hamburger (mobile)
  const hamburger = document.getElementById('hamburger');
  const navLinks = document.querySelector('.nav-links');
  const sidebarOverlay = document.getElementById('user-sidebar-overlay');
  
  if (hamburger) {
    hamburger.addEventListener('click', function() {
      hamburger.classList.toggle('active');
      if (navLinks) navLinks.classList.toggle('active');
      if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
    });
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      hamburger.classList.remove('active');
      if (navLinks) navLinks.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });
  }


  
  // ── Result Modal System ──
  (function buildResultModal() {
    const css = `
      /* ── Result Modal Overlay ── */
      #erix-result-overlay {
        position: fixed; inset: 0; z-index: 99999;
        display: flex; align-items: center; justify-content: center;
        background: rgba(0,0,0,0); pointer-events: none;
        transition: background 0.35s ease;
      }
      #erix-result-overlay.visible {
        background: rgba(0,0,0,0.72); pointer-events: all;
      }

      /* Card */
      #erix-result-card {
        position: relative;
        width: min(92vw, 440px);
        background: #131313;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 20px;
        padding: 52px 36px 40px;
        text-align: center;
        font-family: 'DM Sans', sans-serif;
        box-shadow: 0 32px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.04);
        transform: scale(0.82) translateY(24px);
        opacity: 0;
        transition: transform 0.42s cubic-bezier(0.34,1.56,0.64,1), opacity 0.32s ease;
        overflow: hidden;
      }
      #erix-result-overlay.visible #erix-result-card {
        transform: scale(1) translateY(0);
        opacity: 1;
      }

      /* Glow bar at top */
      #erix-result-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--erix-modal-accent, #D4A017);
        border-radius: 20px 20px 0 0;
        transition: background 0.3s;
      }

      /* Close btn */
      #erix-result-close {
        position: absolute; top: 16px; right: 18px;
        background: rgba(255,255,255,0.06); border: none; cursor: pointer;
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,0.5); font-size: 18px; line-height: 1;
        transition: background 0.2s, color 0.2s;
      }
      #erix-result-close:hover { background: rgba(255,255,255,0.12); color: #fff; }

      /* Icon circle */
      #erix-result-icon-wrap {
        width: 88px; height: 88px; border-radius: 50%;
        margin: 0 auto 24px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.04);
        border: 2px solid rgba(255,255,255,0.08);
        position: relative;
      }
      #erix-result-icon-wrap svg {
        width: 44px; height: 44px;
        stroke-dasharray: 120;
        stroke-dashoffset: 120;
        transition: stroke-dashoffset 0.7s cubic-bezier(0.4,0,0.2,1) 0.18s;
      }
      #erix-result-overlay.visible #erix-result-icon-wrap svg {
        stroke-dashoffset: 0;
      }

      /* Ripple ring animation */
      #erix-result-icon-wrap::after {
        content: '';
        position: absolute; inset: -8px; border-radius: 50%;
        border: 2px solid var(--erix-modal-accent, #D4A017);
        opacity: 0;
        animation: none;
      }
      #erix-result-overlay.visible #erix-result-icon-wrap::after {
        animation: erix-ring-pulse 1.4s ease-out 0.3s forwards;
      }
      @keyframes erix-ring-pulse {
        0%   { transform: scale(0.9); opacity: 0.7; }
        100% { transform: scale(1.35); opacity: 0; }
      }

      /* Title */
      #erix-result-title {
        font-size: 22px; font-weight: 700; letter-spacing: -0.3px;
        color: #fff; margin-bottom: 10px;
      }

      /* Body text */
      #erix-result-body {
        font-size: 15px; line-height: 1.6;
        color: rgba(245,245,240,0.65);
        margin-bottom: 32px;
      }

      /* Action button */
      #erix-result-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 32px; border-radius: 6px;
        border: none; cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        font-size: 15px; font-weight: 600; letter-spacing: 0.3px;
        background: var(--erix-modal-accent, #D4A017); color: #0d0d0d;
        transition: opacity 0.2s, transform 0.2s;
      }
      #erix-result-btn:hover { opacity: 0.88; transform: translateY(-1px); }

      /* Particle dots (decorative) */
      .erix-modal-dot {
        position: absolute; border-radius: 50%;
        background: var(--erix-modal-accent, #D4A017);
        opacity: 0; animation: none;
      }
      #erix-result-overlay.visible .erix-modal-dot {
        animation: erix-dot-pop 0.8s ease-out forwards;
      }
      @keyframes erix-dot-pop {
        0%   { transform: scale(0); opacity: 0.9; }
        60%  { opacity: 0.5; }
        100% { transform: scale(1); opacity: 0; }
      }
    `;

    const styleEl = document.createElement('style');
    styleEl.id = 'erix-result-modal-styles';
    styleEl.textContent = css;
    document.head.appendChild(styleEl);

    // Build overlay + card DOM
    const overlay = document.createElement('div');
    overlay.id = 'erix-result-overlay';
    overlay.innerHTML = `
      <div id="erix-result-card">
        <button id="erix-result-close" aria-label="Close">&times;</button>

        <!-- decorative dots -->
        <span class="erix-modal-dot" style="width:8px;height:8px;top:22%;left:14%;animation-delay:0.35s;animation-duration:1.0s"></span>
        <span class="erix-modal-dot" style="width:5px;height:5px;top:15%;right:20%;animation-delay:0.55s;animation-duration:0.9s"></span>
        <span class="erix-modal-dot" style="width:6px;height:6px;bottom:28%;left:10%;animation-delay:0.45s;animation-duration:1.1s"></span>
        <span class="erix-modal-dot" style="width:4px;height:4px;bottom:20%;right:12%;animation-delay:0.60s;animation-duration:0.85s"></span>

        <div id="erix-result-icon-wrap">
          <svg id="erix-result-icon" viewBox="0 0 44 44" fill="none" stroke="#D4A017" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"></svg>
        </div>
        <h2 id="erix-result-title"></h2>
        <p  id="erix-result-body"></p>
        <button id="erix-result-btn"></button>
      </div>
    `;
    document.body.appendChild(overlay);

    // Close handlers
    document.getElementById('erix-result-close').addEventListener('click', window.erixCloseResultModal = function() {
      overlay.classList.remove('visible');
    });
    overlay.addEventListener('click', e => { if (e.target === overlay) window.erixCloseResultModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') window.erixCloseResultModal(); });
  })();

  // Public API to show the modal
  window.showResultModal = function(type, title, body) {
    const overlay  = document.getElementById('erix-result-overlay');
    const icon     = document.getElementById('erix-result-icon');
    const titleEl  = document.getElementById('erix-result-title');
    const bodyEl   = document.getElementById('erix-result-body');
    const btn      = document.getElementById('erix-result-btn');
    const card     = document.getElementById('erix-result-card');
    const iconWrap = document.getElementById('erix-result-icon-wrap');

    const isSuccess = (type === 'success');
    const accent = isSuccess ? '#D4A017' : '#e53935';
    const iconPath = isSuccess
      ? '<path d="M8 22 L18 32 L36 12"/>'   // checkmark
      : '<path d="M14 14 L30 30 M30 14 L14 30"/>'; // X

    // Apply accent colour
    card.style.setProperty('--erix-modal-accent', accent);
    overlay.style.setProperty('--erix-modal-accent', accent);
    icon.setAttribute('stroke', accent);
    iconWrap.style.borderColor = accent + '40';
    iconWrap.style.background  = accent + '12';
    btn.style.background = isSuccess ? '#D4A017' : accent;
    btn.style.color = isSuccess ? '#0d0d0d' : '#fff';

    icon.innerHTML = iconPath;
    icon.style.strokeDasharray  = '120';
    icon.style.strokeDashoffset = '120';

    titleEl.textContent = title;
    bodyEl.textContent  = body;
    btn.textContent     = isSuccess ? 'Great, thanks!' : 'Got it';
    btn.onclick = window.erixCloseResultModal;

    // Reset + show
    overlay.classList.remove('visible');
    void overlay.offsetWidth; // force reflow
    overlay.classList.add('visible');

    // Reset icon animation by re-triggering
    setTimeout(() => { icon.style.strokeDashoffset = '0'; }, 50);

    // Auto-dismiss success after 5s
    if (isSuccess) setTimeout(window.erixCloseResultModal, 5000);
  };

  // ── Shared form validation helper ──

  function validateContactForm(fields) {
    const errors = {};
    const name = fields.name ? fields.name.trim() : '';
    const phone = fields.phone ? fields.phone.trim() : '';
    const email = fields.email ? fields.email.trim() : '';
    const service = fields.service ? fields.service.trim() : '';
    const message = fields.message ? fields.message.trim() : '';

    if (!name || name.length < 2) errors.name = 'Please enter your full name (at least 2 characters).';
    if (!phone || !/^\d{10}$/.test(phone.replace(/\D/g, ''))) errors.phone = 'Please enter a valid 10-digit phone number.';
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.email = 'Please enter a valid email address.';
    if (!service) errors.service = 'Please select a service.';
    if (!message || message.length < 10) errors.message = 'Please enter a message (at least 10 characters).';

    return errors;
  }

  function showFieldError(inputEl, msg) {
    clearFieldError(inputEl);
    inputEl.classList.add('input-error');
    const err = document.createElement('span');
    err.className = 'field-error-msg';
    err.textContent = msg;
    inputEl.parentNode.appendChild(err);
  }

  function clearFieldError(inputEl) {
    inputEl.classList.remove('input-error');
    const prev = inputEl.parentNode.querySelector('.field-error-msg');
    if (prev) prev.remove();
  }

  function clearAllErrors(formEl) {
    formEl.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    formEl.querySelectorAll('.field-error-msg').forEach(el => el.remove());
  }

  // Inject validation error styles once
  if (!document.getElementById('erix-validation-styles')) {
    const style = document.createElement('style');
    style.id = 'erix-validation-styles';
    style.textContent = `
      .input-error { border-color: #e53935 !important; box-shadow: 0 0 0 2px rgba(229,57,53,0.18) !important; }
      .field-error-msg { display:block; color:#e53935; font-size:12px; margin-top:4px; font-family:'DM Sans',sans-serif; }
    `;
    document.head.appendChild(style);
  }

  // ── Main Contact Form ──
  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    // Clear field errors on input
    contactForm.querySelectorAll('input, select, textarea').forEach(el => {
      el.addEventListener('input', () => clearFieldError(el));
      el.addEventListener('change', () => clearFieldError(el));
    });

    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      clearAllErrors(this);

      const btn    = document.getElementById('formSubmit');
      const status = document.getElementById('formStatus');

      // Gather values
      const formData = new FormData(this);
      const fields = {
        name:    formData.get('from_name'),
        phone:   formData.get('phone'),
        email:   formData.get('from_email'),
        service: formData.get('service'),
        message: formData.get('message')
      };

      // Validate
      const errors = validateContactForm(fields);
      if (Object.keys(errors).length > 0) {
        const nameEl    = this.querySelector('[name="from_name"]');
        const phoneEl   = this.querySelector('[name="phone"]');
        const emailEl   = this.querySelector('[name="from_email"]');
        const serviceEl = this.querySelector('[name="service"]');
        const msgEl     = this.querySelector('[name="message"]');
        if (errors.name    && nameEl)    showFieldError(nameEl, errors.name);
        if (errors.phone   && phoneEl)   showFieldError(phoneEl, errors.phone);
        if (errors.email   && emailEl)   showFieldError(emailEl, errors.email);
        if (errors.service && serviceEl) showFieldError(serviceEl, errors.service);
        if (errors.message && msgEl)     showFieldError(msgEl, errors.message);
        return;
      }

      btn.disabled = true;
      btn.textContent = 'Sending…';
      status.className = 'form-status';
      status.textContent = '';

      // Submit directly to local DB
      const rootPath = typeof ERIX_ROOT !== 'undefined' ? ERIX_ROOT : './';
      fetch(rootPath + 'pages/admin/log_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          full_name: fields.name,
          phone:     fields.phone,
          email:     fields.email,
          service:   fields.service,
          message:   fields.message
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          contactForm.reset();
          showResultModal(
            'success',
            'Message Received!',
            'Thank you for reaching out. Our team will get back to you within 24 hours.'
          );
        } else {
          showResultModal(
            'error',
            'Submission Failed',
            (data.message || 'Something went wrong on our end. Please try again or contact us directly.')
          );
        }
      })
      .catch(() => {
        showResultModal(
          'error',
          'Network Error',
          'Could not connect to the server. Please check your internet connection and try again.'
        );
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Send Message <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>';
      });
    });
  }


  // ── Testimonials slider ──
  const slides = document.querySelectorAll('.testi-slide');
  const dots   = document.querySelectorAll('.testi-dot');
  const testiNext = document.getElementById('testiNext');
  const testiPrev = document.getElementById('testiPrev');
  const testiSlider = document.getElementById('testiSlider');
  let current  = 0;

  function goTo(n) {
    if (slides.length === 0 || dots.length === 0) return;
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
  }

  if (testiNext) {
    testiNext.addEventListener('click', () => goTo(current + 1));
  }
  if (testiPrev) {
    testiPrev.addEventListener('click', () => goTo(current - 1));
  }
  dots.forEach(d => d.addEventListener('click', () => goTo(+d.dataset.i)));

  // Auto-advance every 6s if slider exists
  if (testiSlider && slides.length > 0) {
    let autoPlay = setInterval(() => goTo(current + 1), 6000);
    testiSlider.addEventListener('mouseenter', () => clearInterval(autoPlay));
    testiSlider.addEventListener('mouseleave', () => {
      autoPlay = setInterval(() => goTo(current + 1), 6000);
    });
  }

  // ── Stats Counter Animation ──
  const stats = document.querySelectorAll('.stat-number');
  stats.forEach(stat => {
    const text = stat.textContent.trim();
    const target = parseInt(text.replace(/[^\d]/g, ''), 10);
    const suffix = text.replace(/\d/g, '');
    
    stat.textContent = '0' + suffix;
    
    let started = false;
    
    const animate = () => {
      if (started) return;
      started = true;
      
      // Calculate duration dynamically based on the target value to balance visual speed
      // Clamps the duration between 800ms (for small numbers like 12) and 2200ms (for large numbers like 320)
      const duration = Math.min(800 + (target * 4), 2200);
      const startTime = performance.now();
      
      const update = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing: easeOutQuad
        const easeProgress = progress * (2 - progress);
        const currentValue = Math.floor(easeProgress * target);
        
        stat.textContent = currentValue + suffix;
        
        if (progress < 1) {
          requestAnimationFrame(update);
        } else {
          stat.textContent = target + suffix;
        }
      };
      
      requestAnimationFrame(update);
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const container = entry.target.closest('.hero-stats');
          // If the stats container is still invisible (waiting for CSS fadeUp animation to finish)
          if (container && window.getComputedStyle(container).opacity === '0') {
            container.addEventListener('animationend', () => {
              animate();
            }, { once: true });
          } else {
            animate();
          }
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    
    observer.observe(stat);
  });

  // ── Video Sound Toggle ──
  const soundToggle = document.getElementById('soundToggle');
  const storyVideo = document.getElementById('storyVideo');
  if (soundToggle && storyVideo) {
    const iconOn = soundToggle.querySelector('.sound-icon-on');
    const iconOff = soundToggle.querySelector('.sound-icon-off');

    soundToggle.addEventListener('click', () => {
      if (storyVideo.muted) {
        storyVideo.muted = false;
        iconOff.style.display = 'none';
        iconOn.style.display = 'block';
      } else {
        storyVideo.muted = true;
        iconOff.style.display = 'block';
        iconOn.style.display = 'none';
      }
    });
  }

  // ── Video Full View / Fullscreen Toggle ──
  const fullscreenToggle = document.getElementById('fullscreenToggle');
  if (fullscreenToggle && storyVideo) {
    fullscreenToggle.addEventListener('click', () => {
      if (storyVideo.requestFullscreen) {
        storyVideo.requestFullscreen();
      } else if (storyVideo.webkitRequestFullscreen) { /* Safari / iOS */
        storyVideo.webkitRequestFullscreen();
      } else if (storyVideo.mozRequestFullScreen) { /* Firefox */
        storyVideo.mozRequestFullScreen();
      } else if (storyVideo.msRequestFullscreen) { /* IE/Edge */
        storyVideo.msRequestFullscreen();
      }
    });
  }

  // ── Mobile Carousels (Services & Projects) ──
  const setupCarousel = (wrapperClass, gridClass, dotsId) => {
    const wrapper = document.querySelector(wrapperClass);
    const grid = document.querySelector(gridClass);
    const dotsContainer = document.getElementById(dotsId);

    if (!wrapper || !grid || !dotsContainer) return;

    const cards = grid.children;
    const totalCards = cards.length;
    let currentIndex = 0;

    // Create dots
    for (let i = 0; i < totalCards; i++) {
      const dot = document.createElement('button');
      dot.classList.add('carousel-dot');
      if (i === 0) dot.classList.add('active');
      dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
      dot.addEventListener('click', () => {
        goToSlide(i);
      });
      dotsContainer.appendChild(dot);
    }

    const updateCarousel = () => {
      // Only apply transform if screen is mobile breakpoint
      if (window.innerWidth <= 768) {
        grid.style.transform = `translateX(-${currentIndex * 100}%)`;
      } else {
        grid.style.transform = '';
      }

      // Update dots
      const dots = dotsContainer.querySelectorAll('.carousel-dot');
      dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentIndex);
      });
    };

    const goToSlide = (index) => {
      currentIndex = index;
      updateCarousel();
    };

    // Swipe handlers
    let startX = 0;
    let endX = 0;

    wrapper.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
    }, { passive: true });

    wrapper.addEventListener('touchend', (e) => {
      endX = e.changedTouches[0].clientX;
      const diffX = startX - endX;

      if (Math.abs(diffX) > 50) { // Swipe threshold
        if (diffX > 0 && currentIndex < totalCards - 1) {
          // Swiped left -> next slide
          currentIndex++;
        } else if (diffX < 0 && currentIndex > 0) {
          // Swiped right -> prev slide
          currentIndex--;
        }
        updateCarousel();
      }
    }, { passive: true });

    // Handle resize
    window.addEventListener('resize', () => {
      updateCarousel();
    });
  };

  // Run carousel setup
  setupCarousel('.services-carousel-wrapper', '.services-grid', 'servicesDots');
  setupCarousel('.projects-carousel-wrapper', '.projects-grid', 'projectsDots');
  setupCarousel('.values-carousel-wrapper', '.values-grid', 'valuesDots');

  // ── 3D Card Tilt Effect (Desktop Only) ──
  if (window.innerWidth > 768) {
    const tiltCards = document.querySelectorAll('.project-card');
    tiltCards.forEach(card => {
      card.style.willChange = 'transform, box-shadow';

      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const width = rect.width;
        const height = rect.height;
        
        // Calculate tilt angles (subtle -8 to 8 deg range)
        const rotateX = ((height / 2 - y) / (height / 2)) * 8;
        const rotateY = ((x - width / 2) / (width / 2)) * 8;
        
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.02)`;
        card.style.transition = 'transform 0.1s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.35s ease';
      });

      card.addEventListener('mouseleave', () => {
        card.style.transform = '';
        card.style.transition = 'transform 0.5s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.35s ease';
      });
    });
  }

  // ── POPUP ENQUIRY MODAL LOGIC ──
  (function() {
    const injectModal = () => {
      if (document.getElementById('quoteModal')) return;

      const modalHTML = `
        <div id="quoteModal" class="modal-overlay">
          <div class="modal-content">
            <button class="modal-close" id="modalCloseBtn" aria-label="Close modal">&times;</button>
            <div class="modal-header">
              <div class="blog-details-eyebrow">
                <div class="blog-details-eyebrow-line"></div>
                <span class="blog-details-eyebrow-text">Request Quote</span>
              </div>
              <h2 class="modal-title">Start Your <span>Project</span></h2>
            </div>
            <form id="popupContactForm" novalidate>
              <div class="form-row">
                <div class="form-group">
                  <label for="pname">Full Name</label>
                  <input type="text" id="pname" name="from_name" placeholder="John Doe" required/>
                </div>
                <div class="form-group">
                  <label for="pphone">Phone Number</label>
                  <input type="tel" id="pphone" name="phone" placeholder="Enter 10-digit phone number" pattern="[0-9]{10}" maxlength="10" title="Please enter a valid 10-digit phone number" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required/>
                </div>
              </div>
              <div class="form-group">
                <label for="pemail">Email Address</label>
                <input type="email" id="pemail" name="from_email" placeholder="john@example.com" required/>
              </div>
              <div class="form-group">
                <label for="pservice">Service Required</label>
                <select id="pservice" name="service" required>
                  <option value="" disabled selected>Select a service…</option>
                  <option>Residential Construction</option>
                  <option>Commercial Construction</option>
                  <option>Renovation & Remodeling</option>
                  <option>Interior Fit-Out</option>
                  <option>Other / Not Sure Yet</option>
                </select>
              </div>
              <div class="form-group">
                <label for="pmessage">Your Message</label>
                <textarea id="pmessage" name="message" placeholder="Tell us about your project — location, size, timeline…" required></textarea>
              </div>
              <button type="submit" class="form-submit" id="popupSubmitBtn">
                Send Message
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
              </button>
              <p class="form-status" id="popupStatusMsg"></p>
            </form>
          </div>
        </div>
      `;
      document.body.insertAdjacentHTML('beforeend', modalHTML);

      const modal = document.getElementById('quoteModal');
      const closeBtn = document.getElementById('modalCloseBtn');
      const popupForm = document.getElementById('popupContactForm');

      const openModal = () => {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      };

      const closeModal = () => {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      };

      closeBtn.addEventListener('click', closeModal);
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });

      document.querySelectorAll('.nav-cta').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          openModal();
        });
      });

      const startProjBtn = document.querySelector('.services-cta-link');
      if (startProjBtn) {
        startProjBtn.addEventListener('click', (e) => {
          e.preventDefault();
          openModal();
        });
      }

      const pageName = window.location.pathname.split('/').pop();
      const isHomePage = pageName === 'index.html' || pageName === 'index.php' || pageName === '' || window.location.pathname.endsWith('/');

      if (isHomePage && !sessionStorage.getItem('popupShown')) {
        setTimeout(() => {
          openModal();
          sessionStorage.setItem('popupShown', 'true');
        }, 60000);
      }

      if (popupForm) {
        // Clear field errors on input
        popupForm.querySelectorAll('input, select, textarea').forEach(el => {
          el.addEventListener('input', () => clearFieldError(el));
          el.addEventListener('change', () => clearFieldError(el));
        });

        popupForm.addEventListener('submit', function(e) {
          e.preventDefault();
          clearAllErrors(this);

          const btn    = document.getElementById('popupSubmitBtn');
          const status = document.getElementById('popupStatusMsg');

          // Gather values
          const formData = new FormData(this);
          const fields = {
            name:    formData.get('from_name'),
            phone:   formData.get('phone'),
            email:   formData.get('from_email'),
            service: formData.get('service'),
            message: formData.get('message')
          };

          // Validate
          const errors = validateContactForm(fields);
          if (Object.keys(errors).length > 0) {
            const nameEl    = this.querySelector('[name="from_name"]');
            const phoneEl   = this.querySelector('[name="phone"]');
            const emailEl   = this.querySelector('[name="from_email"]');
            const serviceEl = this.querySelector('[name="service"]');
            const msgEl     = this.querySelector('[name="message"]');
            if (errors.name    && nameEl)    showFieldError(nameEl, errors.name);
            if (errors.phone   && phoneEl)   showFieldError(phoneEl, errors.phone);
            if (errors.email   && emailEl)   showFieldError(emailEl, errors.email);
            if (errors.service && serviceEl) showFieldError(serviceEl, errors.service);
            if (errors.message && msgEl)     showFieldError(msgEl, errors.message);
            return;
          }

          btn.disabled = true;
          btn.textContent = 'Sending…';
          status.className = 'form-status';
          status.textContent = '';

          // Submit directly to local DB
          const rootPath = typeof ERIX_ROOT !== 'undefined' ? ERIX_ROOT : './';
          fetch(rootPath + 'pages/admin/log_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              full_name: fields.name,
              phone:     fields.phone,
              email:     fields.email,
              service:   fields.service,
              message:   fields.message
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              popupForm.reset();
              closeModal();
              showResultModal(
                'success',
                'Message Received!',
                'Thank you for reaching out. Our team will get back to you within 24 hours.'
              );
            } else {
              showResultModal(
                'error',
                'Submission Failed',
                (data.message || 'Something went wrong on our end. Please try again or contact us directly.')
              );
            }
          })
          .catch(() => {
            showResultModal(
              'error',
              'Network Error',
              'Could not connect to the server. Please check your internet connection and try again.'
            );
          })
          .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Send Message <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>';
          });
        });
      }
    };

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', injectModal);
    } else {
      injectModal();
    }
  })();

  // ── COOKIE CONSENT ──
  const cookieConsent = document.getElementById('cookieConsent');
  const acceptCookies = document.getElementById('acceptCookies');
  const declineCookies = document.getElementById('declineCookies');

  if (cookieConsent && !localStorage.getItem('cookieConsent')) {
    setTimeout(() => {
      cookieConsent.classList.add('show');
    }, 2000);
  }

  if (acceptCookies) {
    acceptCookies.addEventListener('click', () => {
      localStorage.setItem('cookieConsent', 'accepted');
      cookieConsent.classList.remove('show');
    });
  }

  if (declineCookies) {
    declineCookies.addEventListener('click', () => {
      localStorage.setItem('cookieConsent', 'declined');
      cookieConsent.classList.remove('show');
    });
  }
