const nav = document.getElementById('mainNav');
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 40);
  });

  // Hamburger (mobile)
  document.getElementById('hamburger').addEventListener('click', function() {
    const links = document.querySelector('.nav-links');
    const open = links.style.display === 'flex';
    links.style.cssText = open
      ? ''
      : 'display:flex;flex-direction:column;position:fixed;top:80px;left:0;right:0;background:rgba(13,13,13,0.97);padding:32px 5%;gap:24px;border-bottom:1px solid rgba(212,160,23,0.2)';
  });


  // ── EmailJS init (replace with your actual Public Key) ──
  if (typeof emailjs !== 'undefined') {
    emailjs.init('YOUR_PUBLIC_KEY');
  }

  const contactForm = document.getElementById('contactForm');
  if (contactForm && typeof emailjs !== 'undefined') {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const btn    = document.getElementById('formSubmit');
      const status = document.getElementById('formStatus');
      btn.disabled = true;
      btn.textContent = 'Sending…';
      status.className = 'form-status';
      status.textContent = '';

      // Replace 'YOUR_SERVICE_ID' and 'YOUR_TEMPLATE_ID' with your EmailJS values
      emailjs.sendForm('YOUR_SERVICE_ID', 'YOUR_TEMPLATE_ID', this)
        .then(() => {
          status.textContent = '✓ Message sent! We will be in touch within 24 hours.';
          status.className = 'form-status success';
          this.reset();
        })
        .catch(() => {
          status.textContent = '✗ Something went wrong. Please try WhatsApp or email us directly.';
          status.className = 'form-status error';
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
