<?php
/**
 * Step 5: Study Design & Methodology
 */
?>

<h4 class="mb-4">
    <i class="bi bi-diagram-3 me-2"></i>
    Study Design & Methodology
</h4>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Study Background</h5>
        
        <div class="mb-3">
            <label for="study_background" class="form-label">Background & Literature Review</label>
            <textarea class="form-control" id="study_background" name="study_background" rows="6" required
                     placeholder="Include relevant African and/or Ghanaian literature with references"><?= htmlspecialchars($step_data['study_background'] ?? '') ?></textarea>
            <div class="form-text">Provide a concise summary of existing research in this area, with particular emphasis on African and Ghanaian context. Include key references.</div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Aims & Objectives</h5>
        
        <div class="mb-3">
            <label for="study_aim" class="form-label">Primary Aim</label>
            <textarea class="form-control" id="study_aim" name="study_aim" rows="2" required
                     placeholder="State the main aim of your study"><?= htmlspecialchars($step_data['study_aim'] ?? '') ?></textarea>
            <div class="form-text">Clearly state the primary aim of your research in one or two sentences.</div>
        </div>
        
        <div class="mb-3">
            <label for="study_objectives" class="form-label">Specific Objectives</label>
            <textarea class="form-control" id="study_objectives" name="study_objectives" rows="4" required
                     placeholder="List your specific research objectives"><?= htmlspecialchars($step_data['study_objectives'] ?? '') ?></textarea>
            <div class="form-text">List the specific objectives that will help achieve your primary aim. Each objective should be clear, measurable, and achievable.</div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Study Framework</h5>
        
        <div class="mb-3">
            <label class="form-label">Study Type</label>
            <select class="form-select" id="study_type" name="study_type" required>
                <option value="">-- Select Study Type --</option>
                <option value="Observational" <?= isset($step_data['study_type']) && $step_data['study_type'] == 'Observational' ? 'selected' : '' ?>>Observational</option>
                <option value="Interventional" <?= isset($step_data['study_type']) && $step_data['study_type'] == 'Interventional' ? 'selected' : '' ?>>Interventional</option>
                <option value="Mixed Methods" <?= isset($step_data['study_type']) && $step_data['study_type'] == 'Mixed Methods' ? 'selected' : '' ?>>Mixed Methods</option>
                <option value="Qualitative" <?= isset($step_data['study_type']) && $step_data['study_type'] == 'Qualitative' ? 'selected' : '' ?>>Qualitative</option>
                <option value="Systematic Review" <?= isset($step_data['study_type']) && $step_data['study_type'] == 'Systematic Review' ? 'selected' : '' ?>>Systematic Review</option>
                <option value="Case Report" <?= isset($step_data['study_type']) && $step_data['study_type'] == 'Case Report' ? 'selected' : '' ?>>Case Report</option>
                <option value="Other" <?= isset($step_data['study_type']) && $step_data['study_type'] == 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        
        <div id="study_type_other_section" class="mb-3" style="display: none;">
            <label for="study_type_other" class="form-label">Please specify study type</label>
            <input type="text" class="form-control" id="study_type_other" name="study_type_other"
                   value="<?= htmlspecialchars($step_data['study_type_other'] ?? '') ?>">
        </div>
        
        <div class="mb-3">
            <label for="conceptual_framework" class="form-label">Study Hypothesis or Conceptual Framework</label>
            <textarea class="form-control" id="conceptual_framework" name="conceptual_framework" rows="4" required
                     placeholder="Describe your study hypothesis or conceptual framework"><?= htmlspecialchars($step_data['conceptual_framework'] ?? '') ?></textarea>
            <div class="form-text">State your research hypothesis or describe the conceptual framework guiding your research.</div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Study Population & Sampling</h5>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="sample_size" class="form-label">Sample Size</label>
                <input type="number" class="form-control" id="sample_size" name="sample_size" min="1" required
                       value="<?= htmlspecialchars($step_data['sample_size'] ?? '') ?>">
                <div class="form-text">Enter the total number of participants/samples in your study.</div>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="sampling_method" class="form-label">Sampling Method</label>
                <select class="form-select" id="sampling_method" name="sampling_method" required>
                    <option value="">-- Select Method --</option>
                    <option value="Random Sampling" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Random Sampling' ? 'selected' : '' ?>>Random Sampling</option>
                    <option value="Stratified Sampling" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Stratified Sampling' ? 'selected' : '' ?>>Stratified Sampling</option>
                    <option value="Cluster Sampling" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Cluster Sampling' ? 'selected' : '' ?>>Cluster Sampling</option>
                    <option value="Convenience Sampling" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Convenience Sampling' ? 'selected' : '' ?>>Convenience Sampling</option>
                    <option value="Purposive Sampling" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Purposive Sampling' ? 'selected' : '' ?>>Purposive Sampling</option>
                    <option value="Snowball Sampling" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Snowball Sampling' ? 'selected' : '' ?>>Snowball Sampling</option>
                    <option value="Total Population" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Total Population' ? 'selected' : '' ?>>Total Population</option>
                    <option value="Other" <?= isset($step_data['sampling_method']) && $step_data['sampling_method'] == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>
        
        <div id="sampling_method_other_section" class="mb-3" style="display: none;">
            <label for="sampling_method_other" class="form-label">Please specify sampling method</label>
            <input type="text" class="form-control" id="sampling_method_other" name="sampling_method_other"
                   value="<?= htmlspecialchars($step_data['sampling_method_other'] ?? '') ?>">
        </div>
        
        <div class="mb-3">
            <label for="inclusion_criteria" class="form-label">Inclusion Criteria</label>
            <textarea class="form-control" id="inclusion_criteria" name="inclusion_criteria" rows="3" required
                     placeholder="List criteria for including participants in your study"><?= htmlspecialchars($step_data['inclusion_criteria'] ?? '') ?></textarea>
        </div>
        
        <div class="mb-3">
            <label for="exclusion_criteria" class="form-label">Exclusion Criteria</label>
            <textarea class="form-control" id="exclusion_criteria" name="exclusion_criteria" rows="3" required
                     placeholder="List criteria for excluding participants from your study"><?= htmlspecialchars($step_data['exclusion_criteria'] ?? '') ?></textarea>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Data Collection & Analysis</h5>
        
        <div class="mb-3">
            <label for="data_collection_methods" class="form-label">Data Collection Methods</label>
            <textarea class="form-control" id="data_collection_methods" name="data_collection_methods" rows="3" required
                     placeholder="Describe how you will collect data"><?= htmlspecialchars($step_data['data_collection_methods'] ?? '') ?></textarea>
        </div>
        
        <div class="mb-3">
            <label for="data_analysis_plan" class="form-label">Data Analysis Plan</label>
            <textarea class="form-control" id="data_analysis_plan" name="data_analysis_plan" rows="3" required
                     placeholder="Describe your approach to data analysis"><?= htmlspecialchars($step_data['data_analysis_plan'] ?? '') ?></textarea>
        </div>
        
        <div class="mb-3">
            <label for="expected_outcome" class="form-label">Expected Outcomes</label>
            <textarea class="form-control" id="expected_outcome" name="expected_outcome" rows="3" required
                     placeholder="Describe the expected outcomes and impact of your study"><?= htmlspecialchars($step_data['expected_outcome'] ?? '') ?></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="start_date" class="form-label">Expected Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required
                       value="<?= htmlspecialchars($step_data['start_date'] ?? '') ?>">
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="end_date" class="form-label">Expected End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required
                       value="<?= htmlspecialchars($step_data['end_date'] ?? '') ?>">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Study type conditional fields
    const studyType = document.getElementById('study_type');
    const studyTypeOtherSection = document.getElementById('study_type_other_section');
    
    function toggleStudyTypeFields() {
        studyTypeOtherSection.style.display = studyType.value === 'Other' ? 'block' : 'none';
        
        // Add/remove required attribute
        const studyTypeOther = document.getElementById('study_type_other');
        studyTypeOther.required = studyType.value === 'Other';
    }
    
    // Sampling method conditional fields
    const samplingMethod = document.getElementById('sampling_method');
    const samplingMethodOtherSection = document.getElementById('sampling_method_other_section');
    
    function toggleSamplingMethodFields() {
        samplingMethodOtherSection.style.display = samplingMethod.value === 'Other' ? 'block' : 'none';
        
        // Add/remove required attribute
        const samplingMethodOther = document.getElementById('sampling_method_other');
        samplingMethodOther.required = samplingMethod.value === 'Other';
    }
    
    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    function validateDates() {
        if (startDate.value && endDate.value) {
            if (new Date(endDate.value) <= new Date(startDate.value)) {
                endDate.setCustomValidity('End date must be after start date');
            } else {
                endDate.setCustomValidity('');
            }
        }
    }
    
    // Set initial states
    toggleStudyTypeFields();
    toggleSamplingMethodFields();
    
    // Add event listeners
    studyType.addEventListener('change', toggleStudyTypeFields);
    samplingMethod.addEventListener('change', toggleSamplingMethodFields);
    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
});
</script>
