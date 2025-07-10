<?php
/**
 * Step 4: Study Site
 */
?>

<h4 class="mb-4">
    <i class="bi bi-geo-alt me-2"></i>
    Study Site
</h4>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Study Location</h5>
        
        <div class="mb-4">
            <label for="study_site" class="form-label">Where do you intend to conduct the study?</label>
            <select id="study_site" name="study_site" class="form-select" required>
                <option value="">-- Select Directorate --</option>
                <option value="Quality Assurance" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Quality Assurance' ? 'selected' : '' ?>>Quality Assurance</option>
                <option value="Planning, Monitoring & Evaluation" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Planning, Monitoring & Evaluation' ? 'selected' : '' ?>>Planning, Monitoring & Evaluation</option>
                <option value="Public Affairs" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Public Affairs' ? 'selected' : '' ?>>Public Affairs</option>
                <option value="Internal Audit" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Internal Audit' ? 'selected' : '' ?>>Internal Audit</option>
                <option value="Human Resource Management" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Human Resource Management' ? 'selected' : '' ?>>Human Resource Management</option>
                <option value="Biostatics" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Biostatics' ? 'selected' : '' ?>>Biostatics</option>
                <option value="Supply Chain Management" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Supply Chain Management' ? 'selected' : '' ?>>Supply Chain Management</option>
                <option value="Security" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Security' ? 'selected' : '' ?>>Security</option>
                <option value="General Administration" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'General Administration' ? 'selected' : '' ?>>General Administration</option>
                <option value="Transfusion Medicine" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Transfusion Medicine' ? 'selected' : '' ?>>Transfusion Medicine</option>
                <option value="Public Health" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Public Health' ? 'selected' : '' ?>>Public Health</option>
                <option value="Psychiatry" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Psychiatry' ? 'selected' : '' ?>>Psychiatry</option>
                <option value="Research and Development" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Research and Development' ? 'selected' : '' ?>>Research and Development</option>
                <option value="Health Insurance" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Health Insurance' ? 'selected' : '' ?>>Health Insurance</option>
                <option value="ICT" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'ICT' ? 'selected' : '' ?>>ICT</option>
                <option value="Social Welfare" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Social Welfare' ? 'selected' : '' ?>>Social Welfare</option>
                <option value="Chaplaincy" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Chaplaincy' ? 'selected' : '' ?>>Chaplaincy</option>
                <option value="Electric Medical Records System" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Electric Medical Records System' ? 'selected' : '' ?>>Electric Medical Records System</option>
                <option value="Project Management" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Project Management' ? 'selected' : '' ?>>Project Management</option>
                <option value="Corporate and Special Services" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Corporate and Special Services' ? 'selected' : '' ?>>Corporate and Special Services</option>
                <option value="Obstetrics and Gynaecology" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Obstetrics and Gynaecology' ? 'selected' : '' ?>>Obstetrics and Gynaecology</option>
                <option value="Surgery" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Surgery' ? 'selected' : '' ?>>Surgery</option>
                <option value="Child Health" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Child Health' ? 'selected' : '' ?>>Child Health</option>
                <option value="Family Medicine" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Family Medicine' ? 'selected' : '' ?>>Family Medicine</option>
                <option value="Anaesthesia and ICU" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Anaesthesia and ICU' ? 'selected' : '' ?>>Anaesthesia and ICU</option>
                <option value="EENT" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'EENT' ? 'selected' : '' ?>>Eye, Ear, Nose and Throat (EENT)</option>
                <option value="Medicine" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Medicine' ? 'selected' : '' ?>>Medicine</option>
                <option value="Radiology" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Radiology' ? 'selected' : '' ?>>Radiology</option>
                <option value="Oncology" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Oncology' ? 'selected' : '' ?>>Oncology</option>
                <option value="Trauma & Orthopaedics" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Trauma & Orthopaedics' ? 'selected' : '' ?>>Trauma & Orthopaedics</option>
                <option value="Emergency Medicine" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Emergency Medicine' ? 'selected' : '' ?>>Emergency Medicine</option>
                <option value="Oral Health" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Oral Health' ? 'selected' : '' ?>>Oral Health</option>
                <option value="Laboratory Services" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Laboratory Services' ? 'selected' : '' ?>>Laboratory Services</option>
                <option value="General Services" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'General Services' ? 'selected' : '' ?>>General Services</option>
                <option value="Domestic Services" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Domestic Services' ? 'selected' : '' ?>>Domestic Services</option>
                <option value="Multiple Directorates" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Multiple Directorates' ? 'selected' : '' ?>>Multiple Directorates</option>
                <option value="Other" <?= isset($step_data['study_site']) && $step_data['study_site'] == 'Other' ? 'selected' : '' ?>>Other (Please specify)</option>
            </select>
        </div>
        
        <div id="other_site_section" class="mb-3" style="display: none;">
            <label for="other_site" class="form-label">Please specify other study site</label>
            <input type="text" class="form-control" id="other_site" name="other_site"
                   value="<?= htmlspecialchars($step_data['other_site'] ?? '') ?>">
        </div>
        
        <div id="multiple_directorates_section" class="mb-3" style="display: none;">
            <label for="multiple_directorates" class="form-label">Please list all directorates</label>
            <textarea class="form-control" id="multiple_directorates" name="multiple_directorates" rows="3"
                     placeholder="List all directorates where you will conduct your study"><?= htmlspecialchars($step_data['multiple_directorates'] ?? '') ?></textarea>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Staff Involvement</h5>
        
        <div class="mb-4">
            <label class="form-label">What category of staff will be involved in your study?</label>
            <div class="form-text mb-2">Check all that apply</div>
            
            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="staff_nurses" name="staff_category[]" value="Nurses"
                               <?= isset($step_data['staff_category']) && in_array('Nurses', (array)$step_data['staff_category']) ? 'checked' : '' ?>>
                        <label for="staff_nurses" class="form-check-label">Nurses</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="staff_doctors" name="staff_category[]" value="Doctors"
                               <?= isset($step_data['staff_category']) && in_array('Doctors', (array)$step_data['staff_category']) ? 'checked' : '' ?>>
                        <label for="staff_doctors" class="form-check-label">Doctors</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="staff_lab_technicians" name="staff_category[]" value="Laboratory Technicians"
                               <?= isset($step_data['staff_category']) && in_array('Laboratory Technicians', (array)$step_data['staff_category']) ? 'checked' : '' ?>>
                        <label for="staff_lab_technicians" class="form-check-label">Laboratory Technicians</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="staff_pharmacists" name="staff_category[]" value="Pharmacists"
                               <?= isset($step_data['staff_category']) && in_array('Pharmacists', (array)$step_data['staff_category']) ? 'checked' : '' ?>>
                        <label for="staff_pharmacists" class="form-check-label">Pharmacists</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="staff_admin" name="staff_category[]" value="Administrative Staff"
                               <?= isset($step_data['staff_category']) && in_array('Administrative Staff', (array)$step_data['staff_category']) ? 'checked' : '' ?>>
                        <label for="staff_admin" class="form-check-label">Administrative Staff</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="staff_not_applicable" name="staff_category[]" value="Not Applicable"
                               <?= isset($step_data['staff_category']) && in_array('Not Applicable', (array)$step_data['staff_category']) ? 'checked' : '' ?>>
                        <label for="staff_not_applicable" class="form-check-label">Not Applicable</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="staff_other" name="staff_category[]" value="Other"
                               <?= isset($step_data['staff_category']) && in_array('Other', (array)$step_data['staff_category']) ? 'checked' : '' ?>>
                        <label for="staff_other" class="form-check-label">Other</label>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="other_staff_section" class="mb-3" style="display: none;">
            <label for="other_staff" class="form-label">Please specify other staff category</label>
            <input type="text" class="form-control" id="other_staff" name="other_staff"
                   value="<?= htmlspecialchars($step_data['other_staff'] ?? '') ?>">
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Resources & Support</h5>
        
        <div class="mb-3">
            <label for="research_equipments" class="form-label">Equipment Requirements</label>
            <textarea class="form-control" id="research_equipments" name="research_equipments" rows="3" required
                     placeholder="List all equipment you will need for your research"><?= htmlspecialchars($step_data['research_equipments'] ?? '') ?></textarea>
            <div class="form-text">Based on your research proposal, which equipment will you use? List all required equipment.</div>
        </div>
        
        <div class="mb-3">
            <label for="support" class="form-label">Institutional Support</label>
            <textarea class="form-control" id="support" name="support" rows="3" required
                     placeholder="Describe physical or financial support you will provide"><?= htmlspecialchars($step_data['support'] ?? '') ?></textarea>
            <div class="form-text">What physical or financial support do you anticipate providing to KATH during and after your study?</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Study site conditional fields
    const studySite = document.getElementById('study_site');
    const otherSiteSection = document.getElementById('other_site_section');
    const multipleDirSection = document.getElementById('multiple_directorates_section');
    
    function toggleStudySiteFields() {
        const selectedValue = studySite.value;
        otherSiteSection.style.display = selectedValue === 'Other' ? 'block' : 'none';
        multipleDirSection.style.display = selectedValue === 'Multiple Directorates' ? 'block' : 'none';
        
        // Add/remove required attributes
        const otherSite = document.getElementById('other_site');
        const multipleDirs = document.getElementById('multiple_directorates');
        
        otherSite.required = selectedValue === 'Other';
        multipleDirs.required = selectedValue === 'Multiple Directorates';
    }
    
    // Staff category conditional fields
    const staffOther = document.getElementById('staff_other');
    const otherStaffSection = document.getElementById('other_staff_section');
    
    function toggleStaffFields() {
        otherStaffSection.style.display = staffOther.checked ? 'block' : 'none';
        
        // Add/remove required attribute
        const otherStaff = document.getElementById('other_staff');
        otherStaff.required = staffOther.checked;
    }
    
    // Set initial states
    toggleStudySiteFields();
    toggleStaffFields();
    
    // Add event listeners
    studySite.addEventListener('change', toggleStudySiteFields);
    staffOther.addEventListener('change', toggleStaffFields);
});
</script>
