<?php
/**
 * Step 3: Research Training
 */
?>

<h4 class="mb-4">
    <i class="bi bi-journal-check me-2"></i>
    Research Training
</h4>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Good Clinical Practice (GCP) Training</h5>
        
        <div class="mb-3">
            <label class="form-label">Have you had Good Clinical Practice (GCP) training in the past three (3) years?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gcp_training" id="gcp_yes" value="Yes" 
                       <?= isset($step_data['gcp_training']) && $step_data['gcp_training'] == 'Yes' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="gcp_yes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gcp_training" id="gcp_no" value="No"
                       <?= isset($step_data['gcp_training']) && $step_data['gcp_training'] == 'No' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="gcp_no">No</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gcp_training" id="gcp_na" value="N/A"
                       <?= isset($step_data['gcp_training']) && $step_data['gcp_training'] == 'N/A' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="gcp_na">Not Applicable</label>
            </div>
        </div>
        
        <div id="gcp_details_section" class="mb-3" style="display: none;">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Note:</strong> GCP training for Principal Investigator is mandatory for all proposals to conduct clinical trials.
            </div>
            
            <label for="gcp_details" class="form-label">Training Details</label>
            <textarea class="form-control mb-3" id="gcp_details" name="gcp_details" rows="3"
                     placeholder="State the name of place and dates of training"><?= htmlspecialchars($step_data['gcp_details'] ?? '') ?></textarea>
            
            <label for="gcp_certificate" class="form-label">Upload Certificate</label>
            <input type="file" class="form-control" id="gcp_certificate" name="gcp_certificate" 
                   accept=".pdf,.jpg,.jpeg,.png">
            
            <?php if (!empty($step_data['gcp_certificate'])): ?>
            <div class="mt-2">
                <strong>Uploaded Certificate:</strong> 
                <a href="<?= htmlspecialchars($step_data['gcp_certificate']) ?>" target="_blank">
                    <?= basename($step_data['gcp_certificate']) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Research Ethics Training</h5>
        
        <div class="mb-3">
            <label class="form-label">Have you attended any research ethics training in the past three years?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="ethics_training" id="ethics_yes" value="Yes"
                       <?= isset($step_data['ethics_training']) && $step_data['ethics_training'] == 'Yes' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="ethics_yes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="ethics_training" id="ethics_no" value="No"
                       <?= isset($step_data['ethics_training']) && $step_data['ethics_training'] == 'No' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="ethics_no">No</label>
            </div>
        </div>
        
        <div id="ethics_details_section" class="mb-3" style="display: none;">
            <label for="ethics_details" class="form-label">Training Details</label>
            <textarea class="form-control mb-3" id="ethics_details" name="ethics_details" rows="3"
                     placeholder="State the name of place and dates of training"><?= htmlspecialchars($step_data['ethics_details'] ?? '') ?></textarea>
            
            <label for="ethics_certificate" class="form-label">Upload Certificate</label>
            <input type="file" class="form-control" id="ethics_certificate" name="ethics_certificate"
                   accept=".pdf,.jpg,.jpeg,.png">
            
            <?php if (!empty($step_data['ethics_certificate'])): ?>
            <div class="mt-2">
                <strong>Uploaded Certificate:</strong> 
                <a href="<?= htmlspecialchars($step_data['ethics_certificate']) ?>" target="_blank">
                    <?= basename($step_data['ethics_certificate']) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Good Laboratory Practice (GLP) Training</h5>
        
        <div class="mb-3">
            <label class="form-label">Have you had Good Laboratory Practice (GLP) training in the past three (3) years?</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="glp_training" id="glp_yes" value="Yes"
                       <?= isset($step_data['glp_training']) && $step_data['glp_training'] == 'Yes' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="glp_yes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="glp_training" id="glp_no" value="No"
                       <?= isset($step_data['glp_training']) && $step_data['glp_training'] == 'No' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="glp_no">No</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="glp_training" id="glp_na" value="N/A"
                       <?= isset($step_data['glp_training']) && $step_data['glp_training'] == 'N/A' ? 'checked' : '' ?> required>
                <label class="form-check-label" for="glp_na">Not Applicable</label>
            </div>
        </div>
        
        <div id="glp_details_section" class="mb-3" style="display: none;">
            <label for="glp_details" class="form-label">Training Details</label>
            <textarea class="form-control mb-3" id="glp_details" name="glp_details" rows="3"
                     placeholder="State the name of place and dates of training"><?= htmlspecialchars($step_data['glp_details'] ?? '') ?></textarea>
            
            <label for="glp_certificate" class="form-label">Upload Certificate</label>
            <input type="file" class="form-control" id="glp_certificate" name="glp_certificate"
                   accept=".pdf,.jpg,.jpeg,.png">
            
            <?php if (!empty($step_data['glp_certificate'])): ?>
            <div class="mt-2">
                <strong>Uploaded Certificate:</strong> 
                <a href="<?= htmlspecialchars($step_data['glp_certificate']) ?>" target="_blank">
                    <?= basename($step_data['glp_certificate']) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // GCP training details toggle
    const gcpYes = document.getElementById('gcp_yes');
    const gcpNo = document.getElementById('gcp_no');
    const gcpNA = document.getElementById('gcp_na');
    const gcpDetailsSection = document.getElementById('gcp_details_section');
    
    function toggleGCPDetails() {
        gcpDetailsSection.style.display = gcpYes.checked ? 'block' : 'none';
        
        // Add/remove required attributes
        const gcpDetails = document.getElementById('gcp_details');
        if (gcpYes.checked) {
            gcpDetails.setAttribute('required', 'required');
        } else {
            gcpDetails.removeAttribute('required');
        }
    }
    
    // Ethics training details toggle
    const ethicsYes = document.getElementById('ethics_yes');
    const ethicsNo = document.getElementById('ethics_no');
    const ethicsDetailsSection = document.getElementById('ethics_details_section');
    
    function toggleEthicsDetails() {
        ethicsDetailsSection.style.display = ethicsYes.checked ? 'block' : 'none';
        
        // Add/remove required attributes
        const ethicsDetails = document.getElementById('ethics_details');
        if (ethicsYes.checked) {
            ethicsDetails.setAttribute('required', 'required');
        } else {
            ethicsDetails.removeAttribute('required');
        }
    }
    
    // GLP training details toggle
    const glpYes = document.getElementById('glp_yes');
    const glpNo = document.getElementById('glp_no');
    const glpNA = document.getElementById('glp_na');
    const glpDetailsSection = document.getElementById('glp_details_section');
    
    function toggleGLPDetails() {
        glpDetailsSection.style.display = glpYes.checked ? 'block' : 'none';
        
        // Add/remove required attributes
        const glpDetails = document.getElementById('glp_details');
        if (glpYes.checked) {
            glpDetails.setAttribute('required', 'required');
        } else {
            glpDetails.removeAttribute('required');
        }
    }
    
    // Set initial states
    toggleGCPDetails();
    toggleEthicsDetails();
    toggleGLPDetails();
    
    // Add event listeners
    gcpYes.addEventListener('change', toggleGCPDetails);
    gcpNo.addEventListener('change', toggleGCPDetails);
    gcpNA.addEventListener('change', toggleGCPDetails);
    
    ethicsYes.addEventListener('change', toggleEthicsDetails);
    ethicsNo.addEventListener('change', toggleEthicsDetails);
    
    glpYes.addEventListener('change', toggleGLPDetails);
    glpNo.addEventListener('change', toggleGLPDetails);
    glpNA.addEventListener('change', toggleGLPDetails);
});
</script>
