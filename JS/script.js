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
  emailjs.init('YOUR_PUBLIC_KEY');

  document.getElementById('contactForm').addEventListener('submit', function(e) {
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

  // ── Testimonials slider ──
  const slides = document.querySelectorAll('.testi-slide');
  const dots   = document.querySelectorAll('.testi-dot');
  let current  = 0;

  function goTo(n) {
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
  }

  document.getElementById('testiNext').addEventListener('click', () => goTo(current + 1));
  document.getElementById('testiPrev').addEventListener('click', () => goTo(current - 1));
  dots.forEach(d => d.addEventListener('click', () => goTo(+d.dataset.i)));

  // Auto-advance every 6s
  let autoPlay = setInterval(() => goTo(current + 1), 6000);
  document.getElementById('testiSlider').addEventListener('mouseenter', () => clearInterval(autoPlay));
  document.getElementById('testiSlider').addEventListener('mouseleave', () => {
    autoPlay = setInterval(() => goTo(current + 1), 6000);
  });

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

