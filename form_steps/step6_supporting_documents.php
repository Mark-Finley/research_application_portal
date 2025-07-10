<?php
/**
 * Step 6: Supporting Documents
 */
?>

<h4 class="mb-4">
    <i class="bi bi-file-earmark me-2"></i>
    Supporting Documents
</h4>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Required Documents</h5>
        <p class="text-muted">Please upload the following required documents</p>
        
        <div class="mb-4">
            <label for="proposal_file" class="form-label">Research Proposal</label>
            <input type="file" class="form-control" id="proposal_file" name="proposal_file" 
                   accept=".pdf,.docx" <?= empty($step_data['proposal_file']) ? 'required' : '' ?>>
            <div class="form-text">Full research proposal document (PDF or DOCX format)</div>
            
            <?php if (!empty($step_data['proposal_file'])): ?>
            <div class="mt-2">
                <strong>Uploaded Document:</strong> 
                <a href="<?= htmlspecialchars($step_data['proposal_file']) ?>" target="_blank">
                    <?= basename($step_data['proposal_file']) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="mb-4">
            <label for="consent_form" class="form-label">Informed Consent Form</label>
            <input type="file" class="form-control" id="consent_form" name="consent_form" 
                   accept=".pdf,.docx" <?= empty($step_data['consent_form']) ? 'required' : '' ?>>
            <div class="form-text">Participant informed consent form document (PDF or DOCX format)</div>
            
            <?php if (!empty($step_data['consent_form'])): ?>
            <div class="mt-2">
                <strong>Uploaded Document:</strong> 
                <a href="<?= htmlspecialchars($step_data['consent_form']) ?>" target="_blank">
                    <?= basename($step_data['consent_form']) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="mb-4">
            <label for="data_collection_tools" class="form-label">Data Collection Tools</label>
            <input type="file" class="form-control" id="data_collection_tools" name="data_collection_tools" 
                   accept=".pdf,.docx" <?= empty($step_data['data_collection_tools']) ? 'required' : '' ?>>
            <div class="form-text">Questionnaires, interview guides, or other data collection instruments (PDF or DOCX format)</div>
            
            <?php if (!empty($step_data['data_collection_tools'])): ?>
            <div class="mt-2">
                <strong>Uploaded Document:</strong> 
                <a href="<?= htmlspecialchars($step_data['data_collection_tools']) ?>" target="_blank">
                    <?= basename($step_data['data_collection_tools']) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Additional Documents</h5>
        <p class="text-muted">Upload these documents if applicable to your study</p>
        
        <div class="accordion" id="additionalDocsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMTA">
                        Material Transfer Agreement (MTA)
                    </button>
                </h2>
                <div id="collapseMTA" class="accordion-collapse collapse" data-bs-parent="#additionalDocsAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="mta_file" name="mta_file" 
                                   accept=".pdf,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Required if your study involves transfer of biological materials</div>
                            
                            <?php if (!empty($step_data['mta_file'])): ?>
                            <div class="mt-2">
                                <strong>Uploaded Document:</strong> 
                                <a href="<?= htmlspecialchars($step_data['mta_file']) ?>" target="_blank">
                                    <?= basename($step_data['mta_file']) ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCTA">
                        Clinical Trial Agreement (CTA)
                    </button>
                </h2>
                <div id="collapseCTA" class="accordion-collapse collapse" data-bs-parent="#additionalDocsAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="cta_file" name="cta_file" 
                                   accept=".pdf,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Required for clinical trials</div>
                            
                            <?php if (!empty($step_data['cta_file'])): ?>
                            <div class="mt-2">
                                <strong>Uploaded Document:</strong> 
                                <a href="<?= htmlspecialchars($step_data['cta_file']) ?>" target="_blank">
                                    <?= basename($step_data['cta_file']) ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDTA">
                        Data Transfer Agreement (DTA)
                    </button>
                </h2>
                <div id="collapseDTA" class="accordion-collapse collapse" data-bs-parent="#additionalDocsAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="dta_file" name="dta_file" 
                                   accept=".pdf,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Required if your study involves transfer of data to external institutions</div>
                            
                            <?php if (!empty($step_data['dta_file'])): ?>
                            <div class="mt-2">
                                <strong>Uploaded Document:</strong> 
                                <a href="<?= htmlspecialchars($step_data['dta_file']) ?>" target="_blank">
                                    <?= basename($step_data['dta_file']) ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFDA">
                        FDA Approval/Documentation
                    </button>
                </h2>
                <div id="collapseFDA" class="accordion-collapse collapse" data-bs-parent="#additionalDocsAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="fda_file" name="fda_file" 
                                   accept=".pdf,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Required for studies involving drugs, devices or interventions requiring FDA approval</div>
                            
                            <?php if (!empty($step_data['fda_file'])): ?>
                            <div class="mt-2">
                                <strong>Uploaded Document:</strong> 
                                <a href="<?= htmlspecialchars($step_data['fda_file']) ?>" target="_blank">
                                    <?= basename($step_data['fda_file']) ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOther">
                        Other Relevant Documents
                    </button>
                </h2>
                <div id="collapseOther" class="accordion-collapse collapse" data-bs-parent="#additionalDocsAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="other_file" name="other_file" 
                                   accept=".pdf,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Any other document relevant for the conduct of the study</div>
                            
                            <?php if (!empty($step_data['other_file'])): ?>
                            <div class="mt-2">
                                <strong>Uploaded Document:</strong> 
                                <a href="<?= htmlspecialchars($step_data['other_file']) ?>" target="_blank">
                                    <?= basename($step_data['other_file']) ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="other_file_description" class="form-label">Document Description</label>
                            <input type="text" class="form-control" id="other_file_description" name="other_file_description"
                                  value="<?= htmlspecialchars($step_data['other_file_description'] ?? '') ?>" 
                                  placeholder="Briefly describe this document">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make "other_file_description" required if other_file is uploaded
    const otherFile = document.getElementById('other_file');
    const otherFileDesc = document.getElementById('other_file_description');
    
    function checkOtherFile() {
        if (otherFile.files.length > 0) {
            otherFileDesc.setAttribute('required', 'required');
        } else {
            otherFileDesc.removeAttribute('required');
        }
    }
    
    // Set initial state
    checkOtherFile();
    
    // Add event listener
    otherFile.addEventListener('change', checkOtherFile);
    
    // Open accordion sections if files are already uploaded
    <?php if (!empty($step_data['mta_file'])): ?>
    document.querySelector('[data-bs-target="#collapseMTA"]').click();
    <?php endif; ?>
    
    <?php if (!empty($step_data['cta_file'])): ?>
    document.querySelector('[data-bs-target="#collapseCTA"]').click();
    <?php endif; ?>
    
    <?php if (!empty($step_data['dta_file'])): ?>
    document.querySelector('[data-bs-target="#collapseDTA"]').click();
    <?php endif; ?>
    
    <?php if (!empty($step_data['fda_file'])): ?>
    document.querySelector('[data-bs-target="#collapseFDA"]').click();
    <?php endif; ?>
    
    <?php if (!empty($step_data['other_file'])): ?>
    document.querySelector('[data-bs-target="#collapseOther"]').click();
    <?php endif; ?>
});
</script>
