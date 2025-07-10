<?php
/**
 * Research Application Form
 * Modular multi-step application form with save and resume functionality
 */
include 'includes/header.php';
require 'config.php';
require 'form_steps/form_config.php';
require 'form_steps/form_utils.php';

// Ensure user has paid
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($_SESSION['user_id']) || !$user) {
    header("Location: login.php");
    exit();
}

if ($user['has_paid'] != 1) {
    header("Location: payment.php");
    exit();
}

// Get current step from URL or default to 1
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Validate step number
if ($current_step < 1 || $current_step > count($form_steps)) {
    $current_step = 1;
}

// Check if we are loading a specific application
$specific_app_id = isset($_GET['app_id']) ? (int)$_GET['app_id'] : null;

// Get application data 
$application = null;
if ($specific_app_id) {
    // Get the specific application by ID
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ? AND user_id = ?");
    $stmt->execute([$specific_app_id, $_SESSION['user_id']]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the application is in a state that shouldn't be edited (pending, under review, etc.)
    if ($application && in_array($application['status'], ['pending', 'under_review', 'submitted'])) {
        // Redirect to dashboard with a message that the application is awaiting review
        header("Location: dashboard.php?message=application_in_review");
        exit;
    }
    
    // If step is not specified but application exists, set step to the next one after completed
    if (!isset($_GET['step']) && $application && isset($application['step_completed'])) {
        $current_step = min((int)$application['step_completed'] + 1, count($form_steps));
    }
} else {
    // Get the most recent application
    $application = getApplicationData($pdo, $_SESSION['user_id']);
}

// Get step data for the current step
$step_data = [];
if ($application && !empty($application['form_data'])) {
    $form_data = json_decode($application['form_data'], true);
    if (is_array($form_data) && isset($form_data[$current_step])) {
        $step_data = $form_data[$current_step];
    }
}

// Custom styles for the modular form
?>
<style>
/* Progress bar styles */
.progress-container {
    padding: 20px 0;
}

.step-indicator {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
    z-index: 2;
    border: 2px solid #e9ecef;
}

.step-indicator.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.step-indicator.completed {
    background-color: #198754;
    color: white;
    border-color: #198754;
}

.step-title {
    font-size: 0.8rem;
    color: #6c757d;
    max-width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Card styles */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1.5rem;
}

.card-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1.25rem;
}

/* Form element styles */
.form-control:focus, .form-select:focus, .form-check-input:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Alert when a form is saved */
#saveAlert {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: none;
}

/* Validation feedback */
.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.was-validated .form-control:invalid,
.form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-control:invalid:focus,
.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.was-validated .form-check-input:invalid,
.form-check-input.is-invalid {
    border-color: #dc3545;
}

.was-validated .form-check-input:invalid:checked,
.form-check-input.is-invalid:checked {
    background-color: #dc3545;
}
</style>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Research Application</h2>
            
            <?php if (isset($application) && !empty($application['ref_code'])): ?>
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <div>
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Application Reference:</strong> <?= htmlspecialchars($application['ref_code']) ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="copyRefCode">
                        <i class="bi bi-clipboard me-1"></i> Copy
                    </button>
                </div>
                <div class="mt-2 small">
                    Save this reference code to resume your application later.
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php if ($_GET['error'] == 'save_failed'): ?>
                    Failed to save your application. Please try again.
                <?php else: ?>
                    An error occurred. Please try again.
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Progress Bar -->
            <?php renderProgressBar($form_steps, $current_step, $application); ?>
            
            <!-- Application Form -->
            <form id="applicationForm" action="form_steps/form_handler.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <!-- Hidden field for current step -->
                <input type="hidden" name="current_step" value="<?= $current_step ?>">
                <?php if ($specific_app_id): ?>
                <input type="hidden" name="app_id" value="<?= $specific_app_id ?>">
                <?php endif; ?>>
                
                <?php include 'form_steps/' . $form_steps[$current_step]['file']; ?>
                
                <!-- Navigation Buttons -->
                <?php renderFormNavigation($current_step, count($form_steps)); ?>
            </form>
            
            <!-- Save Confirmation Alert -->
            <div class="alert alert-success alert-dismissible fade show" id="saveAlert" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="saveAlertMessage">Progress saved successfully!</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('applicationForm');
    const saveButton = document.getElementById('save-progress');
    const saveAlert = document.getElementById('saveAlert');
    const saveAlertMessage = document.getElementById('saveAlertMessage');
    const currentStep = <?= $current_step ?>;
    
    // Copy reference code button
    const copyRefBtn = document.getElementById('copyRefCode');
    if (copyRefBtn) {
        copyRefBtn.addEventListener('click', function() {
            const refCode = '<?= $application['ref_code'] ?? '' ?>';
            navigator.clipboard.writeText(refCode).then(function() {
                copyRefBtn.innerHTML = '<i class="bi bi-check me-1"></i> Copied!';
                setTimeout(() => {
                    copyRefBtn.innerHTML = '<i class="bi bi-clipboard me-1"></i> Copy';
                }, 2000);
            });
        });
    }
    
    // Save progress functionality
    if (saveButton) {
        saveButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Collect form data
            const formData = new FormData(form);
            formData.append('action', 'save_progress');
            formData.append('step', currentStep);
            
            // Show loading indicator
            const originalBtnText = saveButton.innerHTML;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
            saveButton.disabled = true;
            
            // Save via AJAX
            fetch('form_steps/form_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Restore button state
                saveButton.innerHTML = originalBtnText;
                saveButton.disabled = false;
                
                if (data.success) {
                    // Show success message
                    saveAlertMessage.textContent = data.message;
                    saveAlert.style.display = 'block';
                    
                    // Hide after 3 seconds
                    setTimeout(() => {
                        saveAlert.style.display = 'none';
                    }, 3000);
                    
                    // If there's a reference code, update the page to show it without reloading
                    if (data.ref_code && !document.querySelector('.alert-info')) {
                        const refCodeAlert = document.createElement('div');
                        refCodeAlert.className = 'alert alert-info mb-4';
                        refCodeAlert.innerHTML = `
                            <div class="d-flex align-items-center">
                                <div>
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <strong>Application Reference:</strong> ${data.ref_code}
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="copyRefCode">
                                    <i class="bi bi-clipboard me-1"></i> Copy
                                </button>
                            </div>
                            <div class="mt-2 small">
                                Save this reference code to resume your application later.
                            </div>
                        `;
                        
                        form.parentNode.insertBefore(refCodeAlert, form);
                        
                        // Add event listener to the new copy button
                        document.getElementById('copyRefCode').addEventListener('click', function() {
                            navigator.clipboard.writeText(data.ref_code);
                            this.innerHTML = '<i class="bi bi-check me-1"></i> Copied!';
                            setTimeout(() => {
                                this.innerHTML = '<i class="bi bi-clipboard me-1"></i> Copy';
                            }, 2000);
                        });
                    }
                } else {
                    // Show error message
                    saveAlertMessage.textContent = data.message || 'Failed to save progress';
                    saveAlert.className = saveAlert.className.replace('alert-success', 'alert-danger');
                    saveAlert.style.display = 'block';
                    
                    // Hide after 3 seconds
                    setTimeout(() => {
                        saveAlert.style.display = 'none';
                        saveAlert.className = saveAlert.className.replace('alert-danger', 'alert-success');
                    }, 3000);
                }
            })
            .catch(error => {
                // Restore button state
                saveButton.innerHTML = originalBtnText;
                saveButton.disabled = false;
                
                console.error('Error:', error);
                saveAlertMessage.textContent = 'An error occurred while saving';
                saveAlert.className = saveAlert.className.replace('alert-success', 'alert-danger');
                saveAlert.style.display = 'block';
                
                // Hide after 3 seconds
                setTimeout(() => {
                    saveAlert.style.display = 'none';
                    saveAlert.className = saveAlert.className.replace('alert-danger', 'alert-success');
                }, 3000);
            });
        });
    }
    
    // Form navigation
    const prevButton = document.querySelector('.prev-step');
    const nextButton = document.querySelector('.next-step');
    
    if (prevButton) {
        prevButton.addEventListener('click', function() {
            <?php if ($specific_app_id): ?>
            window.location.href = `<?= $_SERVER['PHP_SELF'] ?>?step=${currentStep - 1}&app_id=<?= $specific_app_id ?>`;
            <?php else: ?>
            window.location.href = `<?= $_SERVER['PHP_SELF'] ?>?step=${currentStep - 1}`;
            <?php endif; ?>
        });
    }
    
    if (nextButton) {
        nextButton.addEventListener('click', function(event) {
            // Only validate form when moving to next step
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                
                // Scroll to the first invalid element
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                return;
            }
            
            // Save current step data before navigating to next step
            const formData = new FormData(form);
            formData.append('action', 'save_progress');
            formData.append('step', currentStep);
            
            // Show loading indicator
            const originalBtnText = nextButton.innerHTML;
            nextButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
            nextButton.disabled = true;
            
            fetch('form_steps/form_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Navigate to next step
                    <?php if ($specific_app_id): ?>
                    window.location.href = `<?= $_SERVER['PHP_SELF'] ?>?step=${currentStep + 1}&app_id=<?= $specific_app_id ?>`;
                    <?php else: ?>
                    // If we have an app_id now, use it in the next URL
                    if (data.app_id) {
                        window.location.href = `<?= $_SERVER['PHP_SELF'] ?>?step=${currentStep + 1}&app_id=${data.app_id}`;
                    } else {
                        window.location.href = `<?= $_SERVER['PHP_SELF'] ?>?step=${currentStep + 1}`;
                    }
                    <?php endif; ?>
                } else {
                    // Restore button text and enable it
                    nextButton.innerHTML = originalBtnText;
                    nextButton.disabled = false;
                    
                    // Show error message
                    saveAlertMessage.textContent = data.message || 'Failed to save progress';
                    saveAlert.className = saveAlert.className.replace('alert-success', 'alert-danger');
                    saveAlert.style.display = 'block';
                    
                    // Hide after 3 seconds
                    setTimeout(() => {
                        saveAlert.style.display = 'none';
                        saveAlert.className = saveAlert.className.replace('alert-danger', 'alert-success');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Restore button text and enable it
                nextButton.innerHTML = originalBtnText;
                nextButton.disabled = false;
                
                saveAlertMessage.textContent = 'An error occurred while saving';
                saveAlert.className = saveAlert.className.replace('alert-success', 'alert-danger');
                saveAlert.style.display = 'block';
                
                // Hide after 3 seconds
                setTimeout(() => {
                    saveAlert.style.display = 'none';
                    saveAlert.className = saveAlert.className.replace('alert-danger', 'alert-success');
                }, 3000);
            });
        });
    }
    
    // Form validation
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            
            // Scroll to the first invalid element
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?php include 'includes/footer.php'; ?>