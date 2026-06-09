<?php
$pageTitle = "Contact Us - Erix Construction";
$pathPrefix = "../../";
$currentPage = "contact";
$navClass = "scrolled";
$navLocked = true;
include '../includes/header.php';
?>

<!-- ── MAIN PAGE CONTENT ── -->
<main class="contact-page">

  <!-- Solid-Color Theme Hero Section -->
  <section class="service-hero">
    <div class="service-hero-content">
      <div class="service-hero-eyebrow">
        <div class="service-hero-eyebrow-line"></div>
        <span class="service-hero-eyebrow-text">Let's Build Together</span>
      </div>
      <h1 class="service-hero-title">Contact <span>Us</span></h1>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact" id="contact" style="padding: 80px 5%;">
    <div class="contact-inner">

      <div class="contact-left">
        <div class="section-eyebrow">
          <div class="eyebrow-line"></div>
          <span class="eyebrow-text" style="color:var(--cream);">Get In Touch</span>
        </div>
        <h2 class="section-title">Start Your<br/><span>Project Today</span></h2>
        <p class="contact-desc">Have a project in mind? Fill in the form and our team will get back to you within 24 hours to discuss your vision, timeline, and budget.</p>

        <div class="contact-info-list">
          <div class="contact-info-item">
            <div class="contact-info-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <div>
              <span class="contact-info-label">Phone</span>
              <span class="contact-info-val">+91 98765 43210</span>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
              <span class="contact-info-label">Email</span>
              <span class="contact-info-val">info@erixconstruction.com</span>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
              <span class="contact-info-label">Office</span>
              <span class="contact-info-val">Level 12, Apex Tower, BKC, Mumbai — 400051</span>
            </div>
          </div>
        </div>
      </div>

      <div class="contact-form-wrap">
        <form id="contactForm" novalidate>
          <div class="form-row">
            <div class="form-group">
              <label for="fname">Full Name</label>
              <input type="text" id="fname" name="from_name" placeholder="John Doe" required/>
            </div>
            <div class="form-group">
              <label for="fphone">Phone Number</label>
              <input type="tel" id="fphone" name="phone" placeholder="+91 00000 00000" required/>
            </div>
          </div>
          <div class="form-group">
            <label for="femail">Email Address</label>
            <input type="email" id="femail" name="from_email" placeholder="john@example.com" required/>
          </div>
          <div class="form-group">
            <label for="fservice">Service Required</label>
            <select id="fservice" name="service" required>
              <option value="" disabled selected>Select a service…</option>
              <option>Residential Construction</option>
              <option>Commercial Construction</option>
              <option>Renovation & Remodeling</option>
              <option>Interior Fit-Out</option>
              <option>Other / Not Sure Yet</option>
            </select>
          </div>
          <div class="form-group">
            <label for="fmessage">Your Message</label>
            <textarea id="fmessage" name="message" placeholder="Tell us about your project — location, size, timeline…" required></textarea>
          </div>
          <button type="submit" class="form-submit" id="formSubmit">
            Send Message
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
          </button>
          <p class="form-status" id="formStatus"></p>
        </form>
      </div>

    </div>
  </section>

</main>

<?php include '../includes/footer.php'; ?>
