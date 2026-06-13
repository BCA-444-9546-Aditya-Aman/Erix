<!-- ── FOOTER ── -->
<footer <?php echo isset($footerClass) ? 'class="' . $footerClass . '"' : ''; ?>>
  <div class="footer-top">
    <div class="footer-brand">
      <a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>index.php" class="nav-logo">
        <span class="logo-e">E</span><span class="logo-rix">RIX</span>
        <span class="logo-dot"></span>
      </a>
      <p class="footer-tagline">Engineering Excellence. Constructing the Future. Transforming ideas into structures that last.</p>
      <div class="footer-socials">
        <a href="#" class="social-link" aria-label="Instagram">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
        </a>
        <a href="#" class="social-link" aria-label="LinkedIn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
        </a>
        <a href="#" class="social-link" aria-label="Facebook">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        </a>
        <a href="https://wa.me/919999999999" class="social-link" aria-label="WhatsApp">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.852L.057 23.492a.75.75 0 00.921.921l5.64-1.471A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.848 0-3.592-.476-5.11-1.314l-.368-.213-3.812.994.994-3.812-.213-.368A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        </a>
      </div>
    </div>

    <div class="footer-col">
      <h5>Navigate</h5>
      <ul>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>index.php">Home</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/about.php">About Us</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/services.php">Services</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/projects.php">Projects</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/blogs.php">Blogs</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/contact.php">Contact</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h5>Services</h5>
      <ul>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/services.php">Residential</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/services.php">Commercial</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/services.php">Renovation</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/services.php">Interior Fit-Out</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h5>Contact</h5>
      <ul>
        <li><a href="tel:+919876543210">+91 98765 43210</a></li>
        <li><a href="mailto:info@erixconstruction.com">info@erixconstruction.com</a></li>
        <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/contact.php">BKC, Mumbai — 400051</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p class="footer-copy">&copy; 2030 <span>Erix Construction</span>. All rights reserved.</p>
    <div class="footer-legal">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Use</a>
    </div>
  </div>
</footer>

<!-- ── WHATSAPP FLOATING WIDGET ── -->
<a class="floating-contact" href="https://wa.me/919999999999?text=Hi%20Erix%2C%20I%27d%20like%20to%20discuss%20a%20project."
   target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
  <div class="floating-contact-img-container">
    <img src="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>assets/images/team_member.png" alt="Erix Support" class="floating-contact-img"/>
  </div>
  <div class="floating-contact-text">
    <span class="floating-contact-title">Do You Have A Project To Discuss?</span>
    <span class="floating-contact-subtitle">Let's Chat.</span>
  </div>
  <div class="floating-contact-btn">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
      <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.852L.057 23.492a.75.75 0 00.921.921l5.64-1.471A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.848 0-3.592-.476-5.11-1.314l-.368-.213-3.812.994.994-3.812-.213-.368A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
    </svg>
  </div>
</a>

<!-- ── COOKIE CONSENT ── -->
<div id="cookieConsent" class="cookie-consent">
  <div class="cookie-content">
    <h4>We value your privacy</h4>
    <p>We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.</p>
  </div>
  <div class="cookie-actions">
    <button id="declineCookies" class="btn-decline">Decline</button>
    <button id="acceptCookies" class="btn-accept">Accept All</button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script src="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>JS/script.js"></script>
</body>
</html>
