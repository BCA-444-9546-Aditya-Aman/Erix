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
    });
  </script>
</body>
</html>
