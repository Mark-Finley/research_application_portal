  </main>

<!-- Footer -->
<footer class="admin-footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <p class="mb-0">&copy; <?= date('Y') ?> Research Portal Admin. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-md-end">
        <p class="mb-0">Version 1.0</p>
      </div>
    </div>
  </div>
</footer>

<!-- Dismiss Alert After 5 Seconds -->
<script>
  // Auto dismiss alerts after 5 seconds
  document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
      setTimeout(function() {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 5000);
    });
  });
</script>

<!-- Bootstrap and Main Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebar = document.getElementById('sidebar');
    const adminHeader = document.getElementById('adminHeader');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileToggle');
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Function to handle sidebar toggle
    function toggleSidebar() {
      sidebar.classList.toggle('collapsed');
      adminHeader.classList.toggle('expanded');
      mainContent.classList.toggle('expanded');
      
      // Save state to localStorage
      localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
    
    // Set initial state based on localStorage or default (expanded on desktop, collapsed on mobile)
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true' || (savedState === null && window.innerWidth < 992)) {
      sidebar.classList.add('collapsed');
      adminHeader.classList.add('expanded');
      mainContent.classList.add('expanded');
    }
    
    // Event listeners for sidebar toggle
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Mobile toggle for showing/hiding sidebar
    if (mobileToggle) {
      mobileToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
      });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      if (window.innerWidth < 992) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnMobileToggle = mobileToggle.contains(event.target);
        
        if (!isClickInsideSidebar && !isClickOnMobileToggle && !sidebar.classList.contains('collapsed')) {
          sidebar.classList.add('collapsed');
        }
      }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth < 992) {
        sidebar.classList.add('collapsed');
        adminHeader.classList.add('expanded');
        mainContent.classList.add('expanded');
      }
    });
    
    // Change Password functionality
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const changePasswordForm = document.getElementById('changePasswordForm');
    const passwordAlerts = document.getElementById('passwordAlerts');
    
    if (changePasswordBtn && changePasswordForm) {
      changePasswordBtn.addEventListener('click', function() {
        // Validate form
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        // Reset alerts
        passwordAlerts.innerHTML = '';
        
        // Basic validation
        if (!currentPassword || !newPassword || !confirmPassword) {
          showPasswordAlert('Please fill in all password fields', 'danger');
          return;
        }
        
        if (newPassword !== confirmPassword) {
          showPasswordAlert('New passwords do not match', 'danger');
          return;
        }
        
        // Password strength check
        if (newPassword.length < 8) {
          showPasswordAlert('Password must be at least 8 characters long', 'danger');
          return;
        }
        
        // Submit via AJAX
        const formData = new FormData(changePasswordForm);
        
        fetch('change_password.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showPasswordAlert(data.message, 'success');
            // Reset form
            changePasswordForm.reset();
            // Close modal after 2 seconds on success
            setTimeout(() => {
              const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
              modal.hide();
            }, 2000);
          } else {
            showPasswordAlert(data.message, 'danger');
          }
        })
        .catch(error => {
          showPasswordAlert('An error occurred while changing password', 'danger');
          console.error('Error:', error);
        });
      });
    }
    
    // Function to show password change alerts
    function showPasswordAlert(message, type) {
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      passwordAlerts.appendChild(alertDiv);
    }
  });
</script>

</body>
</html>
