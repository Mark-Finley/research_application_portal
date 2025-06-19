  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const sidebar = document.getElementById("sidebar");
  const toggleSidebar = document.getElementById("toggleSidebar");

  toggleSidebar.addEventListener("click", function () {
    sidebar.classList.toggle("show");
  });

  // Optional: Hide sidebar when clicking outside (for mobile UX)
  document.addEventListener("click", function (event) {
    const isClickInside = sidebar.contains(event.target) || toggleSidebar.contains(event.target);
    if (!isClickInside && window.innerWidth <= 768) {
      sidebar.classList.remove("show");
    }
  });
</script>

</body>
</html>
