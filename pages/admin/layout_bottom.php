  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleBtn = document.getElementById('mobile-menu-toggle');
      const sidebar = document.querySelector('.sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      
      if (toggleBtn && sidebar && overlay) {
        function toggleSidebar() {
          toggleBtn.classList.toggle('active');
          sidebar.classList.toggle('active');
          overlay.classList.toggle('active');
        }
        
        toggleBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
        
        const closeBtn = document.getElementById('mobile-menu-close');
        if (closeBtn) {
          closeBtn.addEventListener('click', toggleSidebar);
        }
        
        // Close sidebar if a link is clicked on mobile
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
          link.addEventListener('click', function() {
            if (sidebar.classList.contains('active')) {
              toggleSidebar();
            }
          });
        });
      }

      // Auto-dismiss alerts after 4 seconds
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
          alert.style.opacity = '0';
          alert.style.transform = 'translateY(-10px)';
          setTimeout(() => alert.remove(), 500); // Remove from DOM after fade out
        }, 4000);
      });
    });
  </script>
</body>
</html>
