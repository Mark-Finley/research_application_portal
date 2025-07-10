<?php
/**
 * Step 7: Review & Submit
 */

// Get combined application data
$display_data = [];
if ($application) {
    $combined_data = getCombinedApplicationData($application);
    $display_data = processFormDataForDisplay($combined_data);
}
?>

<h4 class="mb-4">
    <i class="bi bi-check-circle me-2"></i>
    Review & Submit
</h4>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Application Summary</h5>
        <p class="text-muted">Please review your application details before submission</p>
        
        <?php if ($application && !empty($application['ref_code'])): ?>
            <p><strong>Reference Code:</strong> <?= htmlspecialchars($application['ref_code']) ?></p>
        <?php else: ?>
            <p><strong>Reference Code:</strong> <em>Not yet assigned</em></p>
        <?php endif; ?>
        
        <div class="accordion" id="reviewAccordion">
            <!-- Step 1: Study Information -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#reviewStep1">
                        <i class="bi bi-info-circle me-2"></i> Study Information
                    </button>
                </h2>
                <div id="reviewStep1" class="accordion-collapse collapse show" data-bs-parent="#reviewAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <?php 
                                    $step1_fields = ['purpose', 'category', 'research_title', 'keywords'];
                                    foreach ($step1_fields as $field): 
                                        if (isset($display_data[$field])):
                                    ?>
                                    <tr>
                                        <th width="30%"><?= formatFieldName($field) ?></th>
                                        <td><?= displayFieldValue($display_data[$field], $field) ?></td>
                                    </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    
                                    // Handle conditional fields
                                    if (isset($display_data['category']) && $display_data['category'] == 'Study has been waived' && isset($display_data['waiver_code'])):
                                    ?>
                                    <tr>
                                        <th>Waiver Code</th>
                                        <td><?= displayFieldValue($display_data['waiver_code'], 'waiver_code') ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?step=1" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Principal Investigator -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reviewStep2">
                        <i class="bi bi-person me-2"></i> Principal Investigator
                    </button>
                </h2>
                <div id="reviewStep2" class="accordion-collapse collapse" data-bs-parent="#reviewAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <?php 
                                    $step2_fields = ['title', 'surname', 'firstname', 'othernames', 'nationality', 'institution', 'directorate', 'college', 'local_collab_details', 'supervisor_details'];
                                    foreach ($step2_fields as $field): 
                                        if (isset($display_data[$field]) && !empty($display_data[$field])):
                                    ?>
                                    <tr>
                                        <th width="30%"><?= formatFieldName($field) ?></th>
                                        <td><?= displayFieldValue($display_data[$field], $field) ?></td>
                                    </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?step=2" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Research Training -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reviewStep3">
                        <i class="bi bi-journal-check me-2"></i> Research Training
                    </button>
                </h2>
                <div id="reviewStep3" class="accordion-collapse collapse" data-bs-parent="#reviewAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <?php 
                                    $step3_fields = ['gcp_training', 'gcp_details', 'gcp_certificate', 'ethics_training', 'ethics_details', 'ethics_certificate', 'glp_training', 'glp_details', 'glp_certificate'];
                                    foreach ($step3_fields as $field): 
                                        if (isset($display_data[$field]) && !empty($display_data[$field])):
                                    ?>
                                    <tr>
                                        <th width="30%"><?= formatFieldName($field) ?></th>
                                        <td><?= displayFieldValue($display_data[$field], $field) ?></td>
                                    </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?step=3" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Study Site -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reviewStep4">
                        <i class="bi bi-geo-alt me-2"></i> Study Site
                    </button>
                </h2>
                <div id="reviewStep4" class="accordion-collapse collapse" data-bs-parent="#reviewAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <?php 
                                    $step4_fields = ['study_site', 'other_site', 'multiple_directorates', 'staff_category', 'other_staff', 'research_equipments', 'support'];
                                    foreach ($step4_fields as $field): 
                                        if (isset($display_data[$field]) && !empty($display_data[$field])):
                                    ?>
                                    <tr>
                                        <th width="30%"><?= formatFieldName($field) ?></th>
                                        <td><?= displayFieldValue($display_data[$field], $field) ?></td>
                                    </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?step=4" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Step 5: Study Design -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reviewStep5">
                        <i class="bi bi-diagram-3 me-2"></i> Study Design & Methodology
                    </button>
                </h2>
                <div id="reviewStep5" class="accordion-collapse collapse" data-bs-parent="#reviewAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <?php 
                                    $step5_fields = ['study_background', 'study_aim', 'study_objectives', 'study_type', 'study_type_other', 'conceptual_framework', 'sample_size', 'sampling_method', 'sampling_method_other', 'inclusion_criteria', 'exclusion_criteria', 'data_collection_methods', 'data_analysis_plan', 'expected_outcome', 'start_date', 'end_date'];
                                    foreach ($step5_fields as $field): 
                                        if (isset($display_data[$field]) && !empty($display_data[$field])):
                                    ?>
                                    <tr>
                                        <th width="30%"><?= formatFieldName($field) ?></th>
                                        <td><?= displayFieldValue($display_data[$field], $field) ?></td>
                                    </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?step=5" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Step 6: Supporting Documents -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reviewStep6">
                        <i class="bi bi-file-earmark me-2"></i> Supporting Documents
                    </button>
                </h2>
                <div id="reviewStep6" class="accordion-collapse collapse" data-bs-parent="#reviewAccordion">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <?php 
                                    $step6_fields = ['proposal_file', 'consent_form', 'data_collection_tools', 'mta_file', 'cta_file', 'dta_file', 'fda_file', 'other_file', 'other_file_description'];
                                    foreach ($step6_fields as $field): 
                                        if (isset($display_data[$field]) && !empty($display_data[$field])):
                                    ?>
                                    <tr>
                                        <th width="30%"><?= formatFieldName($field) ?></th>
                                        <td><?= displayFieldValue($display_data[$field], $field) ?></td>
                                    </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>?step=6" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Declaration & Submission</h5>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> By submitting this application, you confirm that all information provided is accurate and complete to the best of your knowledge.
        </div>
        
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="confirm_review" name="confirm_review" required>
            <label class="form-check-label" for="confirm_review">
                I confirm that all information provided is accurate and complete to the best of my knowledge.
            </label>
        </div>
        
        <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="confirm_ethics" name="confirm_ethics" required>
            <label class="form-check-label" for="confirm_ethics">
                I understand that making any false statement(s) for the purposes of securing R&D approval and conducting studies in KATH
                is prohibited. KATH reserves the right to suspend or stop my study if later it is found I have falsified information.
            </label>
        </div>
        
        <!-- Hidden fields to hold the values -->
        <input type="hidden" name="step_submit" value="1">
        <input type="hidden" name="current_step" value="7">
    </div>
</div>
