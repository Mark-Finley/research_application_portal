<?php
/**
 * Configuration file for the modular form system
 * Contains settings and structure for the multi-step application form
 */

// Define the form steps and their properties
$form_steps = [
    1 => [
        'name' => 'study_information',
        'title' => 'Study Information',
        'file' => 'step1_study_information.php',
        'icon' => 'bi-info-circle'
    ],
    2 => [
        'name' => 'principal_investigator',
        'title' => 'Principal Investigator Info',
        'file' => 'step2_principal_investigator.php',
        'icon' => 'bi-person'
    ],
    3 => [
        'name' => 'research_training',
        'title' => 'Research Training',
        'file' => 'step3_research_training.php',
        'icon' => 'bi-journal-check'
    ],
    4 => [
        'name' => 'study_site',
        'title' => 'Study Site',
        'file' => 'step4_study_site.php',
        'icon' => 'bi-geo-alt'
    ],
    5 => [
        'name' => 'study_design',
        'title' => 'Study Design & Methodology',
        'file' => 'step5_study_design.php',
        'icon' => 'bi-diagram-3'
    ],
    6 => [
        'name' => 'supporting_documents',
        'title' => 'Supporting Documents',
        'file' => 'step6_supporting_documents.php',
        'icon' => 'bi-file-earmark'
    ],
    7 => [
        'name' => 'review',
        'title' => 'Review & Submit',
        'file' => 'step7_review.php',
        'icon' => 'bi-check-circle'
    ]
];

/**
 * Get application data for a user
 * 
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @return array|false Application data or false if not found
 */
function getApplicationData($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Save application data for a step
 * 
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @param int $step_number Step number
 * @param array $data Form data to save
 * @param int|null $app_id Specific application ID to update (optional)
 * @return bool Success status
 */
function saveApplicationStep($pdo, $user_id, $step_number, $data, $app_id = null) {
    try {
        // If app_id is provided, check if it exists and belongs to the user
        if ($app_id) {
            $stmt = $pdo->prepare("SELECT id, form_data FROM applications WHERE id = ? AND user_id = ?");
            $stmt->execute([$app_id, $user_id]);
            $application = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Check if application exists
            $stmt = $pdo->prepare("SELECT id, form_data FROM applications WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $application = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        $current_time = date('Y-m-d H:i:s');
        
        // Extract specific fields to store in dedicated columns
        $specific_columns = [];
        
        // Process specific fields based on the step
        if ($step_number == 1) {
            // Step 1: Study Information
            if (isset($data['purpose'])) $specific_columns['purpose'] = $data['purpose'];
            if (isset($data['category'])) $specific_columns['category'] = $data['category'];
            
            // Set research_category if category is set
            if (isset($data['category'])) $specific_columns['research_category'] = $data['category'];
        }
        
        if ($step_number == 2) {
            // Step 2: Principal Investigator
            $investigator_name = '';
            if (isset($data['title'])) $investigator_name .= $data['title'] . ' ';
            if (isset($data['firstname'])) $investigator_name .= $data['firstname'] . ' ';
            if (isset($data['surname'])) $investigator_name .= $data['surname'];
            if (!empty($investigator_name)) $specific_columns['investigator_name'] = trim($investigator_name);
            
            // Investigator institution
            $institution_parts = [];
            if (isset($data['KATH']) && $data['KATH']) $institution_parts[] = 'KATH';
            if (isset($data['KNUST']) && $data['KNUST']) $institution_parts[] = 'KNUST';
            if (isset($data['CCTH']) && $data['CCTH']) $institution_parts[] = 'CCTH';
            if (isset($data['KBTH']) && $data['KBTH']) $institution_parts[] = 'KBTH';
            if (isset($data['TTH']) && $data['TTH']) $institution_parts[] = 'TTH';
            if (isset($data['other']) && $data['other']) $institution_parts[] = 'Other';
            
            if (!empty($institution_parts)) {
                $specific_columns['investigator_institution'] = implode(', ', $institution_parts);
            }
            
            // Department
            if (isset($data['directorate'])) $specific_columns['investigator_department'] = $data['directorate'];
            else if (isset($data['college'])) $specific_columns['investigator_department'] = $data['college'];
        }
        
        if ($step_number == 4) {
            // Step 4: Study Site
            if (isset($data['study_site'])) $specific_columns['study_site'] = $data['study_site'];
        }
        
        if ($step_number == 5) {
            // Step 5: Study Design
            if (isset($data['sample_size'])) $specific_columns['sample_size'] = intval($data['sample_size']);
            if (isset($data['researchTitle'])) $specific_columns['study_title'] = $data['researchTitle'];
            if (isset($data['submissionDate'])) $specific_columns['expected_completion_date'] = $data['submissionDate'];
            if (isset($data['study_background'])) $specific_columns['summary'] = substr($data['study_background'], 0, 1000); // Limit summary size
        }
        
        if ($step_number == 6) {
            // Step 6: Supporting Documents
            if (isset($data['proposal_file'])) $specific_columns['proposal_file_path'] = $data['proposal_file'];
            if (isset($data['consent_form'])) $specific_columns['consent_form_path'] = $data['consent_form'];
            if (isset($data['ethics_approval'])) $specific_columns['ethics_approval_path'] = $data['ethics_approval'];
            
            // Also store in document_path for backward compatibility
            if (isset($data['proposal_file'])) $specific_columns['document_path'] = $data['proposal_file'];
        }
        
        if ($application) {
            // Update existing application
            $app_id = $application['id'];
            
            // Get current form data or initialize empty object
            $form_data = [];
            if (!empty($application['form_data'])) {
                $form_data = json_decode($application['form_data'], true) ?: [];
            }
            
            // Update the step data
            $form_data[$step_number] = $data;
            
            // JSON encode the updated data
            $form_data_json = json_encode($form_data);
            
            // Prepare SQL for specific columns
            if (!empty($specific_columns)) {
                $sql_parts = [];
                $params = [$form_data_json, $step_number];
                
                foreach ($specific_columns as $column => $value) {
                    $sql_parts[] = "$column = ?";
                    $params[] = $value;
                }
                
                // Add application ID at the end
                $params[] = $app_id;
                
                $sql = "UPDATE applications SET form_data = ?, step_completed = GREATEST(COALESCE(step_completed, 0), ?), " . 
                       implode(", ", $sql_parts) . " WHERE id = ?";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($params);
            } else {
                // Update just the form_data and step_completed
                $stmt = $pdo->prepare("UPDATE applications SET form_data = ?, step_completed = GREATEST(COALESCE(step_completed, 0), ?) WHERE id = ?");
                $result = $stmt->execute([$form_data_json, $step_number, $app_id]);
            }
            
            if (!$result) {
                error_log("Error updating application: " . print_r($stmt->errorInfo(), true));
            }
            
            return $result;
        } else {
            // Create new application with reference code
            $ref_code = generateReferenceCode();
            
            // Initialize form data with current step
            $form_data = [$step_number => $data];
            $form_data_json = json_encode($form_data);
            
            // Prepare SQL for specific columns
            if (!empty($specific_columns)) {
                $columns = ['user_id', 'ref_code', 'form_data', 'step_completed', 'submitted_at', 'status'];
                $placeholders = ['?', '?', '?', '?', '?', '?'];
                $params = [$user_id, $ref_code, $form_data_json, $step_number, $current_time, 'incomplete'];
                
                foreach ($specific_columns as $column => $value) {
                    $columns[] = $column;
                    $placeholders[] = '?';
                    $params[] = $value;
                }
                
                $sql = "INSERT INTO applications (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute($params);
            } else {
                // Simple insert with just the basic fields
                $stmt = $pdo->prepare("INSERT INTO applications (user_id, ref_code, form_data, step_completed, submitted_at, status) VALUES (?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([$user_id, $ref_code, $form_data_json, $step_number, $current_time, 'incomplete']);
            }
            
            if (!$result) {
                error_log("Error creating application: " . print_r($stmt->errorInfo(), true));
            }
            
            return $result;
        }
    } catch (Exception $e) {
        error_log("Exception in saveApplicationStep: " . $e->getMessage());
        return false;
    }
}

/**
 * Get complete application data by combining both JSON and specific columns
 * 
 * @param array $application Application data from database
 * @return array Combined application data
 */
function getCombinedApplicationData($application) {
    if (!$application) return [];
    
    $result = [
        'id' => $application['id'],
        'user_id' => $application['user_id'],
        'ref_code' => $application['ref_code'],
        'status' => $application['status'],
        'submitted_at' => $application['submitted_at'],
        'step_completed' => $application['step_completed'] ?? 1
    ];
    
    // Add specific columns if they exist
    $specific_columns = [
        'study_title', 'research_category', 'summary', 'document_path',
        'purpose', 'category', 'investigator_name', 'investigator_institution',
        'investigator_department', 'study_site', 'sample_size', 'expected_completion_date',
        'proposal_file_path', 'consent_form_path', 'ethics_approval_path'
    ];
    
    foreach ($specific_columns as $column) {
        if (isset($application[$column]) && !empty($application[$column])) {
            $result[$column] = $application[$column];
        }
    }
    
    // Add form data if it exists
    if (!empty($application['form_data'])) {
        $form_data = json_decode($application['form_data'], true);
        if (is_array($form_data)) {
            $result['form_data'] = $form_data;
            
            // Flatten form data for each step into the result
            foreach ($form_data as $step => $step_data) {
                if (is_array($step_data)) {
                    foreach ($step_data as $key => $value) {
                        // Don't overwrite specific columns with form data
                        if (!isset($result[$key])) {
                            $result[$key] = $value;
                        }
                    }
                }
            }
        }
    }
    
    return $result;
}

/**
 * Generate a unique reference code for an application
 * 
 * @return string Reference code
 */
function generateReferenceCode() {
    return 'RA-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

/**
 * Check if a step is completed
 * 
 * @param array $application Application data
 * @param int $step_number Step number to check
 * @return bool Whether step is completed
 */
function isStepCompleted($application, $step_number) {
    if (!$application) return false;
    return isset($application['step_completed']) && $application['step_completed'] >= $step_number;
}

/**
 * Get form data for a specific step
 * 
 * @param array $application Application data
 * @param int $step_number Step number
 * @return array Step data or empty array if not found
 */
function getStepData($application, $step_number) {
    if (!$application || empty($application['form_data'])) return [];
    
    $form_data = json_decode($application['form_data'], true);
    return $form_data[$step_number] ?? [];
}
