<?php
/**
 * Admin Settings Page
 * 
 * This page allows administrators to configure system settings for the research portal.
 */
require 'header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require '../config.php';

// Initialize variables for settings
$settings = [];
$message = '';
$error = '';

// Function to get all settings from database
function getSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_group, setting_name");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $settings = [];
    foreach ($results as $row) {
        $settings[$row['setting_name']] = $row;
    }
    
    return $settings;
}

// Check if settings table exists, if not create it
$tableExists = $pdo->query("SHOW TABLES LIKE 'settings'")->rowCount() > 0;
if (!$tableExists) {
    $pdo->exec("CREATE TABLE settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_name VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_type VARCHAR(50) NOT NULL DEFAULT 'text',
        setting_group VARCHAR(50) NOT NULL DEFAULT 'general',
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Insert default settings
    $defaultSettings = [
        // General settings
        ['site_name', 'Research Portal', 'text', 'general', 'The name of the research portal'],
        ['admin_email', 'admin@example.com', 'email', 'general', 'Primary admin contact email'],
        ['max_file_size', '10', 'number', 'general', 'Maximum file upload size in MB'],
        ['enable_maintenance_mode', '0', 'boolean', 'general', 'Enable maintenance mode'],
        ['maintenance_message', 'The system is currently under maintenance. Please check back later.', 'textarea', 'general', 'Message to display during maintenance mode'],
        
        // Application settings
        ['enable_applications', '1', 'boolean', 'applications', 'Allow new applications to be submitted'],
        ['require_profile_completion', '1', 'boolean', 'applications', 'Require users to complete their profile before submitting applications'],
        ['max_applications_per_user', '3', 'number', 'applications', 'Maximum number of applications a user can submit'],
        ['default_application_status', 'incomplete', 'select', 'applications', 'Default status for new applications'],
        ['required_documents', 'Research Proposal,Ethics Approval', 'text', 'applications', 'Required documents (comma separated)'],
        
        // Email settings
        ['enable_email_notifications', '1', 'boolean', 'email', 'Send email notifications for application status changes'],
        ['from_email', 'noreply@example.com', 'email', 'email', 'From email address for system emails'],
        ['from_name', 'Research Portal', 'text', 'email', 'From name for system emails'],
        ['approval_email_subject', 'Your research application has been approved', 'text', 'email', 'Subject for approval emails'],
        ['rejection_email_subject', 'Your research application has been rejected', 'text', 'email', 'Subject for rejection emails']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO settings (setting_name, setting_value, setting_type, setting_group, description) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($defaultSettings as $setting) {
        $stmt->execute($setting);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    try {
        $pdo->beginTransaction();
        
        foreach ($_POST as $key => $value) {
            // Skip non-settings fields
            if ($key === 'save_settings') continue;
            
            // Handle boolean values
            if (is_array($value) && isset($value['type']) && $value['type'] === 'boolean') {
                $value = isset($value['value']) ? '1' : '0';
            }
            
            // Check if setting exists and update
            $stmt = $pdo->prepare("SELECT id FROM settings WHERE setting_name = ?");
            $stmt->execute([$key]);
            
            if ($stmt->rowCount() > 0) {
                $updateStmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
                $updateStmt->execute([$value, $key]);
            }
        }
        
        $pdo->commit();
        $message = "Settings updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error updating settings: " . $e->getMessage();
    }
}

// Get current settings
$settings = getSettings($pdo);

// Group settings by their group
$groupedSettings = [];
foreach ($settings as $setting) {
    $group = $setting['setting_group'];
    if (!isset($groupedSettings[$group])) {
        $groupedSettings[$group] = [];
    }
    $groupedSettings[$group][] = $setting;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .admin-header {
      background: linear-gradient(135deg, #007b55 0%, #005f3d 100%);
      color: white;
      padding: 20px;
      border-bottom: 4px solid #005f3d;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .admin-header h2 {
      margin: 0;
      font-weight: 600;
    }
    .settings-card {
      border-radius: 10px;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      margin-bottom: 24px;
      overflow: hidden;
    }
    .settings-card .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      padding: 15px 20px;
    }
    .settings-card .card-header h5 {
      margin: 0;
      font-weight: 600;
    }
    .settings-card .card-body {
      padding: 20px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-label {
      font-weight: 500;
    }
    .setting-description {
      color: #6c757d;
      font-size: 0.875rem;
      margin-top: 5px;
    }
    .settings-icon {
      font-size: 1.5rem;
      margin-right: 10px;
      opacity: 0.7;
    }
    .form-check-input:checked {
      background-color: #007b55;
      border-color: #007b55;
    }
    .btn-save {
      background-color: #007b55;
      border-color: #007b55;
    }
    .btn-save:hover {
      background-color: #005f3d;
      border-color: #005f3d;
    }
    .nav-pills .nav-link.active {
      background-color: #007b55;
    }
    .nav-pills .nav-link {
      color: #343a40;
    }
    .nav-pills .nav-link:hover:not(.active) {
      background-color: #f0f0f0;
    }
  </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center">
    <h2><i class="bi bi-sliders me-2"></i> System Settings</h2>
  </div>
  <div>
    <a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-speedometer2 me-1"></i> Back to Dashboard</a>
  </div>
</div>

<div class="container my-4">
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h4 class="card-title"><i class="bi bi-gear-fill me-2"></i>System Configuration</h4>
          <p class="text-muted mb-0">Configure various settings that control the behavior of the research portal system.</p>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Settings Navigation -->
    <div class="col-md-3 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
          <h5 class="mb-0"><i class="bi bi-list me-2"></i>Settings Categories</h5>
        </div>
        <div class="card-body p-0">
          <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
            <?php 
            $first = true;
            foreach (array_keys($groupedSettings) as $group): 
              $groupLabel = ucfirst($group);
              $groupIcon = match($group) {
                'general' => 'bi-gear',
                'applications' => 'bi-file-earmark-text',
                'email' => 'bi-envelope',
                default => 'bi-box'
              };
            ?>
              <button class="nav-link <?= $first ? 'active' : '' ?>" 
                      id="<?= $group ?>-tab" 
                      data-bs-toggle="pill" 
                      data-bs-target="#<?= $group ?>-settings" 
                      type="button" 
                      role="tab" 
                      aria-controls="<?= $group ?>-settings" 
                      aria-selected="<?= $first ? 'true' : 'false' ?>">
                <i class="bi <?= $groupIcon ?> me-2"></i> <?= $groupLabel ?> Settings
              </button>
            <?php 
              $first = false;
            endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Settings Form -->
    <div class="col-md-9">
      <form method="post" action="">
        <div class="tab-content" id="settings-tabContent">
          <?php 
          $first = true;
          foreach ($groupedSettings as $group => $groupSettings): 
            $groupLabel = ucfirst($group);
          ?>
            <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" 
                 id="<?= $group ?>-settings" 
                 role="tabpanel" 
                 aria-labelledby="<?= $group ?>-tab">
              
              <div class="settings-card">
                <div class="card-header d-flex align-items-center">
                  <h5 class="mb-0">
                    <?php 
                    $groupIconLarge = match($group) {
                      'general' => 'bi-gear-fill',
                      'applications' => 'bi-file-earmark-text',
                      'email' => 'bi-envelope-fill',
                      default => 'bi-box'
                    };
                    ?>
                    <i class="bi <?= $groupIconLarge ?> settings-icon"></i>
                    <?= $groupLabel ?> Settings
                  </h5>
                </div>
                <div class="card-body">
                  <?php foreach ($groupSettings as $setting): ?>
                    <div class="form-group">
                      <label for="<?= $setting['setting_name'] ?>" class="form-label">
                        <?= ucwords(str_replace('_', ' ', $setting['setting_name'])) ?>
                      </label>
                      
                      <?php if ($setting['setting_type'] === 'text' || $setting['setting_type'] === 'email' || $setting['setting_type'] === 'number'): ?>
                        <input type="<?= $setting['setting_type'] ?>" 
                               class="form-control" 
                               id="<?= $setting['setting_name'] ?>" 
                               name="<?= $setting['setting_name'] ?>" 
                               value="<?= htmlspecialchars($setting['setting_value']) ?>">
                      
                      <?php elseif ($setting['setting_type'] === 'textarea'): ?>
                        <textarea class="form-control" 
                                  id="<?= $setting['setting_name'] ?>" 
                                  name="<?= $setting['setting_name'] ?>" 
                                  rows="3"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                      
                      <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                        <div class="form-check form-switch">
                          <input type="hidden" name="<?= $setting['setting_name'] ?>[type]" value="boolean">
                          <input class="form-check-input" 
                                 type="checkbox" 
                                 id="<?= $setting['setting_name'] ?>" 
                                 name="<?= $setting['setting_name'] ?>[value]" 
                                 <?= $setting['setting_value'] == '1' ? 'checked' : '' ?>>
                        </div>
                      
                      <?php elseif ($setting['setting_type'] === 'select' && $setting['setting_name'] === 'default_application_status'): ?>
                        <select class="form-select" id="<?= $setting['setting_name'] ?>" name="<?= $setting['setting_name'] ?>">
                          <option value="incomplete" <?= $setting['setting_value'] === 'incomplete' ? 'selected' : '' ?>>Incomplete</option>
                          <option value="pending" <?= $setting['setting_value'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                          <option value="submitted" <?= $setting['setting_value'] === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                        </select>
                      <?php endif; ?>
                      
                      <div class="setting-description"><?= htmlspecialchars($setting['description']) ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php 
            $first = false;
          endforeach; ?>
        </div>

        <div class="d-flex justify-content-end mb-4">
          <button type="submit" name="save_settings" class="btn btn-primary btn-save">
            <i class="bi bi-save me-2"></i>Save Settings
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-auto">
  <div class="container text-center">
    <p class="text-muted mb-0">Research Application Portal - Admin Settings &copy; <?= date('Y') ?></p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Auto-dismiss alerts after 5 seconds
  document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);
  });
</script>
</body>
</html>
