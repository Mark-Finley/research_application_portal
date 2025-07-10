<?php
/**
 * Form handler for processing form submissions
 */
session_start();
require_once '../config.php';
require_once 'form_config.php';
require_once 'form_utils.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['success' => false, 'message' => '', 'redirect' => ''];

// Handle save progress action
if (isset($_POST['action']) && $_POST['action'] === 'save_progress') {
    if (!isset($_POST['step'])) {
        $response['message'] = 'Missing required step data';
        echo json_encode($response);
        exit;
    }
    
    $step = (int)$_POST['step'];
    // Collect all form data except for 'action' and 'step'
    $form_data = $_POST;
    unset($form_data['action']);
    unset($form_data['step']);
    
    // Check if we have a specific app_id to update
    $specific_app_id = null;
    if (isset($form_data['app_id']) && !empty($form_data['app_id'])) {
        $specific_app_id = (int)$form_data['app_id'];
        unset($form_data['app_id']); // Remove from the form data to prevent it being saved
    }
    
    // Handle file uploads
    foreach ($_FILES as $field_name => $file_info) {
        if ($file_info['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_dir = '../uploads/';
            
            // Make sure the uploads directory exists
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_path = handleFileUpload($file_info, $upload_dir, $user_id);
            
            if ($file_path) {
                $form_data[$field_name] = $file_path;
            }
        }
    }
    
    // For debugging
    error_log("Saving form data for user: $user_id, step: $step, app_id: " . ($specific_app_id ?? 'new'));
    
    // If we have a specific app_id, make sure it belongs to the current user before updating
    if ($specific_app_id) {
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE id = ? AND user_id = ?");
        $stmt->execute([$specific_app_id, $user_id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$app) {
            $specific_app_id = null; // Reset if the app doesn't belong to this user
        }
    }
    
    // Save data
    if (saveApplicationStep($pdo, $user_id, $step, $form_data, $specific_app_id)) {
        $response['success'] = true;
        $response['message'] = 'Progress saved successfully';
        
        // Get the application data to include reference code and app_id
        $application = getApplicationData($pdo, $user_id);
        if ($application) {
            if (isset($application['ref_code'])) {
                $response['ref_code'] = $application['ref_code'];
            }
            if (isset($application['id'])) {
                $response['app_id'] = $application['id'];
            }
        }
    } else {
        $response['message'] = 'Failed to save progress';
        error_log("Failed to save progress for user: $user_id, step: $step");
    }
    
    echo json_encode($response);
    exit;
}

// Handle step submission (for final step)
if (isset($_POST['step_submit'])) {
    if (!isset($_POST['current_step'])) {
        header("Location: ../application.php?error=missing_step");
        exit;
    }
    
    $step = (int)$_POST['current_step'];
    $form_data = $_POST;
    
    // Check if we have a specific app_id to update
    $specific_app_id = null;
    if (isset($form_data['app_id']) && !empty($form_data['app_id'])) {
        $specific_app_id = (int)$form_data['app_id'];
    }
    
    // Remove non-form fields
    unset($form_data['step_submit']);
    unset($form_data['current_step']);
    unset($form_data['app_id']);
    
    // Handle file uploads
    foreach ($_FILES as $field_name => $file_info) {
        if ($file_info['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_dir = '../uploads/';
            $file_path = handleFileUpload($file_info, $upload_dir, $user_id);
            
            if ($file_path) {
                $form_data[$field_name] = $file_path;
            }
        }
    }
    
    // Save step data
    if (saveApplicationStep($pdo, $user_id, $step, $form_data, $specific_app_id)) {
        // Calculate next step
        $next_step = $step + 1;
        
        // If this was the final step, mark application as submitted
        if ($step == count($form_steps)) {
            // Get combined application data
            $application = getApplicationData($pdo, $user_id);
            $combined_data = getCombinedApplicationData($application);
            
            // Update study_title if not already set
            $update_fields = ['status' => 'pending'];
            
            if (empty($combined_data['study_title']) && isset($combined_data['researchTitle'])) {
                $update_fields['study_title'] = $combined_data['researchTitle'];
            }
            
            // Build SQL update statement
            $sql_parts = [];
            $params = [];
            
            foreach ($update_fields as $field => $value) {
                $sql_parts[] = "$field = ?";
                $params[] = $value;
            }
            
            // Add submitted_at timestamp
            $sql_parts[] = "submitted_at = NOW()";
            
            // Use specific app_id for the WHERE clause if available, otherwise use user_id
            if ($specific_app_id) {
                $params[] = $specific_app_id;
                $sql = "UPDATE applications SET " . implode(", ", $sql_parts) . " WHERE id = ?";
            } else {
                // Fallback to user_id, but use the most recent application
                $params[] = $user_id;
                $sql = "UPDATE applications SET " . implode(", ", $sql_parts) . " WHERE user_id = ? ORDER BY id DESC LIMIT 1";
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            header('Location: ../dashboard.php?submitted=1');
            exit;
        } else {
            // Redirect to next step with app_id if available
            $redirect_url = "../application.php?step={$next_step}";
            
            // Get the application to find its ID
            $application = getApplicationData($pdo, $user_id);
            if ($application && isset($application['id'])) {
                $redirect_url .= "&app_id=" . $application['id'];
            }
            
            header("Location: " . $redirect_url);
            exit;
        }
    } else {
        // Handle error - redirect back to same step with error message
        header("Location: ../application.php?step={$step}&error=save_failed");
        exit;
    }
}

// If we get here, it's an invalid request
$response['message'] = 'Invalid request';
echo json_encode($response);
exit;
