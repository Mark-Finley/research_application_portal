<?php
/**
 * Step 1: Study Information
 */
?>

<h4 class="mb-4">
    <i class="bi bi-info-circle me-2"></i>
    Study Information
</h4>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Purpose of Study</h5>
        
        <div class="mb-3">
            <label for="purpose" class="form-label">Select the purpose of your study</label>
            <select class="form-select" id="purpose" name="purpose" required>
                <option value="" selected disabled>-- Select Purpose --</option>
                <option value="Non-Degree" <?= isset($step_data['purpose']) && $step_data['purpose'] == 'Non-Degree' ? 'selected' : '' ?>>Non-Degree</option>
                <option value="Diploma" <?= isset($step_data['purpose']) && $step_data['purpose'] == 'Diploma' ? 'selected' : '' ?>>Diploma</option>
                <option value="1st Degree" <?= isset($step_data['purpose']) && $step_data['purpose'] == '1st Degree' ? 'selected' : '' ?>>1st Degree</option>
                <option value="2nd Degree" <?= isset($step_data['purpose']) && $step_data['purpose'] == '2nd Degree' ? 'selected' : '' ?>>2nd Degree</option>
                <option value="PHD" <?= isset($step_data['purpose']) && $step_data['purpose'] == 'PHD' ? 'selected' : '' ?>>PHD</option>
                <option value="Fellowship" <?= isset($step_data['purpose']) && $step_data['purpose'] == 'Fellowship' ? 'selected' : '' ?>>Fellowship</option>
                <option value="Membership" <?= isset($step_data['purpose']) && $step_data['purpose'] == 'Membership' ? 'selected' : '' ?>>Membership</option>
            </select>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Study Category</h5>
        <p class="text-muted">Select the category that best describes your research study</p>
        
        <div class="mb-3">
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_kath" value="Sponsored by KATH" 
                    <?= isset($step_data['category']) && $step_data['category'] == 'Sponsored by KATH' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_kath">
                    Sponsored by KATH / Unit Budgets
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_local" value="Sponsored by Local Ghanaian Organizations"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Sponsored by Local Ghanaian Organizations' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_local">
                    Sponsored by Local Ghanaian Organizations
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_intl_med" 
                    value="With International Funding (US$ 15,000.00 - 500,000.00)"
                    <?= isset($step_data['category']) && $step_data['category'] == 'With International Funding (US$ 15,000.00 - 500,000.00)' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_intl_med">
                    With International Funding (US$ 15,000.00 - 500,000.00)
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_intl_low" 
                    value="With International Funding (Less than US$ 15,000.00)"
                    <?= isset($step_data['category']) && $step_data['category'] == 'With International Funding (Less than US$ 15,000.00)' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_intl_low">
                    With International Funding (Less than US$ 15,000.00)
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_intl_high" 
                    value="With Substantial International Grants/Contracts Research (Above US$ 500,000.00)"
                    <?= isset($step_data['category']) && $step_data['category'] == 'With Substantial International Grants/Contracts Research (Above US$ 500,000.00)' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_intl_high">
                    With Substantial International Grants/Contracts Research (Above US$ 500,000.00)
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_non_gh" 
                    value="Non-Ghanaian Inverstigators with no External Support"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Non-Ghanaian Inverstigators with no External Support' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_non_gh">
                    Non-Ghanaian Inverstigators with no External Support
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_gh_prof_non_kath" 
                    value="Ghanaian Lecturers/ Professionals (Non KATH Employees without Funding"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Ghanaian Lecturers/ Professionals (Non KATH Employees without Funding' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_gh_prof_non_kath">
                    Ghanaian Lecturers/ Professionals (Non KATH Employees without Funding)
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_gh_prof_kath" 
                    value="Ghanaian Lecturers / Professionals (KATH Employees without Funding"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Ghanaian Lecturers / Professionals (KATH Employees without Funding' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_gh_prof_kath">
                    Ghanaian Lecturers / Professionals (KATH Employees without Funding)
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_pg_intl" 
                    value="Post Graduate Students with International Funding"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Post Graduate Students with International Funding' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_pg_intl">
                    Post Graduate Students with International Funding
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_pg_local" 
                    value="Post Graduate Students with Local Funding"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Post Graduate Students with Local Funding' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_pg_local">
                    Post Graduate Students with Local Funding
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_pg_no_funding" 
                    value="Post Graduate Students (Without Funding)"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Post Graduate Students (Without Funding)' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_pg_no_funding">
                    Post Graduate Students (Without Funding)
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_undergrad" 
                    value="Undergraduate Students"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Undergraduate Students' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_undergrad">
                    Undergraduate Students
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_non_gh_students" 
                    value="Non-Ghanaian Students with no External Support"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Non-Ghanaian Students with no External Support' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_non_gh_students">
                    Non-Ghanaian Students with no External Support
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_gh_prof_low_funding" 
                    value="Ghanaian Lecturers / Professionals (With Funding less than US$ 5,000.00)"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Ghanaian Lecturers / Professionals (With Funding less than US$ 5,000.00)' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_gh_prof_low_funding">
                    Ghanaian Lecturers / Professionals (With Funding less than US$ 5,000.00)
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_case_report" 
                    value="Case Report"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Case Report' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_case_report">
                    Case Report
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="category" id="category_waived" 
                    value="Study has been waived"
                    <?= isset($step_data['category']) && $step_data['category'] == 'Study has been waived' ? 'checked' : '' ?>>
                <label class="form-check-label" for="category_waived">
                    Study has been waived
                </label>
            </div>
        </div>
        
        <!-- Waived Study reference number (conditionally shown) -->
        <div id="waiver_code_section" class="mb-3" style="display: none;">
            <label for="waiver_code" class="form-label">Waiver Reference Number</label>
            <input type="text" class="form-control" id="waiver_code" name="waiver_code" 
                   value="<?= htmlspecialchars($step_data['waiver_code'] ?? '') ?>"
                   placeholder="Enter the reference number for your waived study">
            <div class="form-text">Please enter the reference number provided when your study was waived.</div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Study Title</h5>
        
        <div class="mb-3">
            <label for="research_title" class="form-label">Full title of the research study</label>
            <input type="text" class="form-control" id="research_title" name="research_title" required
                   value="<?= htmlspecialchars($step_data['research_title'] ?? '') ?>"
                   placeholder="Enter the full title of your research study">
        </div>
        
        <div class="mb-3">
            <label for="keywords" class="form-label">Keywords</label>
            <input type="text" class="form-control" id="keywords" name="keywords"
                   value="<?= htmlspecialchars($step_data['keywords'] ?? '') ?>"
                   placeholder="Enter keywords separated by commas">
            <div class="form-text">Enter relevant keywords separated by commas (e.g., clinical trial, diabetes, treatment)</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle waiver code input based on category selection
    const categoryWaived = document.getElementById('category_waived');
    const waiverCodeSection = document.getElementById('waiver_code_section');
    
    function toggleWaiverCode() {
        waiverCodeSection.style.display = categoryWaived.checked ? 'block' : 'none';
        
        // Add/remove required attribute based on visibility
        const waiverCodeInput = document.getElementById('waiver_code');
        if (categoryWaived.checked) {
            waiverCodeInput.setAttribute('required', 'required');
        } else {
            waiverCodeInput.removeAttribute('required');
        }
    }
    
    // Set initial state
    toggleWaiverCode();
    
    // Add event listener
    categoryWaived.addEventListener('change', toggleWaiverCode);
    
    // Add event listeners to all category radio buttons
    document.querySelectorAll('input[name="category"]').forEach(function(radio) {
        radio.addEventListener('change', toggleWaiverCode);
    });
});
</script>
