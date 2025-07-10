<?php
/**
 * Form utilities for the modular application form
 */

/**
 * Render the progress bar for the form
 * 
 * @param array $form_steps Form steps configuration
 * @param int $current_step Current step number
 * @param array $application Application data to check completion status
 * @return void
 */
function renderProgressBar($form_steps, $current_step, $application = null) {
    echo '<div class="progress-container mb-5">';
    echo '<div class="progress" style="height: 8px;">';
    echo '<div class="progress-bar bg-success" role="progressbar" style="width: ' . (($current_step - 1) / (count($form_steps) - 1) * 100) . '%"></div>';
    echo '</div>';
    
    echo '<div class="position-relative mt-2">';
    echo '<div class="d-flex justify-content-between">';
    
    foreach ($form_steps as $step_num => $step) {
        $is_active = $step_num == $current_step;
        $is_completed = $application && isStepCompleted($application, $step_num);
        $status_class = $is_active ? 'active' : ($is_completed ? 'completed' : '');
        
        echo '<div class="text-center position-relative" style="flex: 1;">';
        echo '<div class="step-indicator ' . $status_class . '">';
        
        if ($is_completed && !$is_active) {
            echo '<i class="bi bi-check-lg"></i>';
        } else {
            echo $step_num;
        }
        
        echo '</div>';
        echo '<small class="step-title d-none d-md-block mt-2">' . htmlspecialchars($step['title']) . '</small>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Render the form navigation buttons
 * 
 * @param int $current_step Current step number
 * @param int $total_steps Total number of steps
 * @return void
 */
function renderFormNavigation($current_step, $total_steps) {
    echo '<div class="form-navigation d-flex justify-content-between mt-4">';
    
    if ($current_step > 1) {
        echo '<button type="button" class="btn btn-outline-secondary prev-step"><i class="bi bi-arrow-left me-2"></i>Previous</button>';
    } else {
        echo '<div></div>'; // Empty div for flex spacing
    }
    
    echo '<button type="button" id="save-progress" class="btn btn-secondary"><i class="bi bi-save me-2"></i>Save Progress</button>';
    
    if ($current_step < $total_steps) {
        echo '<button type="button" class="btn btn-primary next-step">Next<i class="bi bi-arrow-right ms-2"></i></button>';
    } else {
        echo '<input type="hidden" name="step_submit" value="1">';
        echo '<button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-2"></i>Submit Application</button>';
    }
    
    echo '</div>';
}

/**
 * Convert form data to a readable format
 * 
 * @param array $data Form data
 * @return array Processed data for display
 */
function processFormDataForDisplay($data) {
    $processed = [];
    
    // Special handling for nested arrays and file paths
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            if (!empty($value)) {
                // Process arrays differently based on their structure
                $arrayValues = [];
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        // Skip nested arrays or handle them recursively if needed
                        continue;
                    } else {
                        $arrayValues[] = $v;
                    }
                }
                $processed[$key] = implode(", ", array_filter($arrayValues));
            } else {
                $processed[$key] = '';
            }
        } else if (strpos($key, 'file') !== false && !empty($value)) {
            $processed[$key] = basename($value);
        } else {
            $processed[$key] = $value;
        }
    }
    
    return $processed;
}

/**
 * Format field name for display
 * 
 * @param string $field_name Field name
 * @return string Formatted field name
 */
function formatFieldName($field_name) {
    return ucwords(str_replace('_', ' ', $field_name));
}

/**
 * Handle file upload
 * 
 * @param array $file $_FILES array item
 * @param string $target_dir Target directory
 * @param string $prefix File prefix (optional)
 * @return string|false Path to uploaded file or false on failure
 */
function handleFileUpload($file, $target_dir, $prefix = '') {
    // Check if file is actually uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Generate unique filename
    $filename = $prefix . '_' . time() . '_' . basename($file['name']);
    $target_file = $target_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $target_file;
    }
    
    return false;
}

/**
 * Display field value with proper formatting
 * 
 * @param mixed $value Field value
 * @param string $field_name Field name for context
 * @return string Formatted value
 */
function displayFieldValue($value, $field_name) {
    if (empty($value)) {
        return '<em class="text-muted">Not provided</em>';
    }
    
    // Special handling for file paths
    if (strpos($field_name, 'file') !== false || strpos($field_name, 'certificate') !== false) {
        $filename = basename($value);
        return '<a href="' . htmlspecialchars($value) . '" target="_blank">' . htmlspecialchars($filename) . '</a>';
    }
    
    // Handle different data types
    if (is_array($value)) {
        return htmlspecialchars(implode(", ", $value));
    } else {
        return htmlspecialchars($value);
    }
}
