<?php
/**
 * Step 2: Principal Investigator Info
 */
?>

<h4 class="mb-4">
    <i class="bi bi-person me-2"></i>
    Principal Investigator Information
</h4>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Personal Information</h5>
        
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="title" class="form-label">Title</label>
                <select class="form-select" id="title" name="title" required>
                    <option value="" selected disabled>-- Select --</option>
                    <option value="Mr." <?= isset($step_data['title']) && $step_data['title'] == 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                    <option value="Mrs." <?= isset($step_data['title']) && $step_data['title'] == 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                    <option value="Ms." <?= isset($step_data['title']) && $step_data['title'] == 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                    <option value="Dr." <?= isset($step_data['title']) && $step_data['title'] == 'Dr.' ? 'selected' : '' ?>>Dr.</option>
                    <option value="Prof." <?= isset($step_data['title']) && $step_data['title'] == 'Prof.' ? 'selected' : '' ?>>Prof.</option>
                    <option value="Rev." <?= isset($step_data['title']) && $step_data['title'] == 'Rev.' ? 'selected' : '' ?>>Rev.</option>
                    <option value="Sir" <?= isset($step_data['title']) && $step_data['title'] == 'Sir' ? 'selected' : '' ?>>Sir</option>
                    <option value="Miss" <?= isset($step_data['title']) && $step_data['title'] == 'Miss' ? 'selected' : '' ?>>Miss</option>
                </select>
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control" id="surname" name="surname" required
                       value="<?= htmlspecialchars($step_data['surname'] ?? '') ?>">
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required
                       value="<?= htmlspecialchars($step_data['firstname'] ?? '') ?>">
            </div>
            
            <div class="col-md-3 mb-3">
                <label for="othernames" class="form-label">Other Name(s)</label>
                <input type="text" class="form-control" id="othernames" name="othernames"
                       value="<?= htmlspecialchars($step_data['othernames'] ?? '') ?>">
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label d-block">Nationality</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nationality" id="nationality_ghanaian" value="Ghanaian" required
                       <?= isset($step_data['nationality']) && $step_data['nationality'] == 'Ghanaian' ? 'checked' : '' ?>>
                <label class="form-check-label" for="nationality_ghanaian">Ghanaian</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="nationality" id="nationality_other" value="Non-Ghanaian" required
                       <?= isset($step_data['nationality']) && $step_data['nationality'] == 'Non-Ghanaian' ? 'checked' : '' ?>>
                <label class="form-check-label" for="nationality_other">Non-Ghanaian</label>
            </div>
        </div>
        
        <div id="nationality_details" class="mb-3" style="display: none;">
            <label for="nationality_specific" class="form-label">Specify Nationality</label>
            <input type="text" class="form-control" id="nationality_specific" name="nationality_specific"
                   value="<?= htmlspecialchars($step_data['nationality_specific'] ?? '') ?>">
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Affiliation & Collaboration</h5>
        
        <div class="mb-4">
            <label for="principal_investigator_institution" class="form-label">Principal Investigator Institution</label>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="institution_kath" name="institution[]" value="KATH"
                               <?= isset($step_data['institution']) && in_array('KATH', (array)$step_data['institution']) ? 'checked' : '' ?>>
                        <label for="institution_kath" class="form-check-label">KATH</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="institution_knust" name="institution[]" value="KNUST"
                               <?= isset($step_data['institution']) && in_array('KNUST', (array)$step_data['institution']) ? 'checked' : '' ?>>
                        <label for="institution_knust" class="form-check-label">KNUST</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="institution_ccth" name="institution[]" value="CCTH"
                               <?= isset($step_data['institution']) && in_array('CCTH', (array)$step_data['institution']) ? 'checked' : '' ?>>
                        <label for="institution_ccth" class="form-check-label">CCTH</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="institution_kbth" name="institution[]" value="KBTH"
                               <?= isset($step_data['institution']) && in_array('KBTH', (array)$step_data['institution']) ? 'checked' : '' ?>>
                        <label for="institution_kbth" class="form-check-label">KBTH</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="institution_tth" name="institution[]" value="TTH"
                               <?= isset($step_data['institution']) && in_array('TTH', (array)$step_data['institution']) ? 'checked' : '' ?>>
                        <label for="institution_tth" class="form-check-label">TTH</label>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="institution_other" name="institution[]" value="Other"
                               <?= isset($step_data['institution']) && in_array('Other', (array)$step_data['institution']) ? 'checked' : '' ?>>
                        <label for="institution_other" class="form-check-label">Other</label>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="institution_other_details" class="mb-3" style="display: none;">
            <label for="institution_other_specific" class="form-label">Specify Other Institution</label>
            <input type="text" class="form-control" id="institution_other_specific" name="institution_other_specific"
                   value="<?= htmlspecialchars($step_data['institution_other_specific'] ?? '') ?>">
        </div>
        
        <div id="kath_directorate_section" class="mb-3" style="display: none;">
            <label for="directorate" class="form-label">Directorate/Unit of Principal Investigator if KATH</label>
            <select id="directorate" name="directorate" class="form-select">
                <option value="">-- Select Directorate --</option>
                <option value="Quality Assurance" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Quality Assurance' ? 'selected' : '' ?>>Quality Assurance</option>
                <option value="Planning, Monitoring & Evaluation" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Planning, Monitoring & Evaluation' ? 'selected' : '' ?>>Planning, Monitoring & Evaluation</option>
                <option value="Public Affairs" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Public Affairs' ? 'selected' : '' ?>>Public Affairs</option>
                <option value="Internal Audit" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Internal Audit' ? 'selected' : '' ?>>Internal Audit</option>
                <option value="Human Resource Management" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Human Resource Management' ? 'selected' : '' ?>>Human Resource Management</option>
                <option value="Biostatics" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Biostatics' ? 'selected' : '' ?>>Biostatics</option>
                <option value="Supply Chain Management" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Supply Chain Management' ? 'selected' : '' ?>>Supply Chain Management</option>
                <option value="Security" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Security' ? 'selected' : '' ?>>Security</option>
                <option value="General Administration" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'General Administration' ? 'selected' : '' ?>>General Administration</option>
                <option value="Transfusion Medicine" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Transfusion Medicine' ? 'selected' : '' ?>>Transfusion Medicine</option>
                <option value="Public Health" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Public Health' ? 'selected' : '' ?>>Public Health</option>
                <option value="Psychiatry" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Psychiatry' ? 'selected' : '' ?>>Psychiatry</option>
                <option value="Research and Development" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Research and Development' ? 'selected' : '' ?>>Research and Development</option>
                <option value="Health Insurance" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Health Insurance' ? 'selected' : '' ?>>Health Insurance</option>
                <option value="ICT" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'ICT' ? 'selected' : '' ?>>ICT</option>
                <option value="Social Welfare" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Social Welfare' ? 'selected' : '' ?>>Social Welfare</option>
                <option value="Chaplaincy" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Chaplaincy' ? 'selected' : '' ?>>Chaplaincy</option>
                <option value="Electric Medical Records System" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Electric Medical Records System' ? 'selected' : '' ?>>Electric Medical Records System</option>
                <option value="Project Management" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Project Management' ? 'selected' : '' ?>>Project Management</option>
                <option value="Corporate and Special Services" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Corporate and Special Services' ? 'selected' : '' ?>>Corporate and Special Services</option>
                <option value="Obstetrics and Gynaecology" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Obstetrics and Gynaecology' ? 'selected' : '' ?>>Obstetrics and Gynaecology</option>
                <option value="Surgery" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Surgery' ? 'selected' : '' ?>>Surgery</option>
                <option value="Child Health" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Child Health' ? 'selected' : '' ?>>Child Health</option>
                <option value="Family Medicine" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Family Medicine' ? 'selected' : '' ?>>Family Medicine</option>
                <option value="Anaesthesia and ICU" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Anaesthesia and ICU' ? 'selected' : '' ?>>Anaesthesia and ICU</option>
                <option value="EENT" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'EENT' ? 'selected' : '' ?>>Eye, Ear, Nose and Throat (EENT)</option>
                <option value="Medicine" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Medicine' ? 'selected' : '' ?>>Medicine</option>
                <option value="Radiology" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Radiology' ? 'selected' : '' ?>>Radiology</option>
                <option value="Oncology" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Oncology' ? 'selected' : '' ?>>Oncology</option>
                <option value="Trauma & Orthopaedics" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Trauma & Orthopaedics' ? 'selected' : '' ?>>Trauma & Orthopaedics</option>
                <option value="Emergency Medicine" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Emergency Medicine' ? 'selected' : '' ?>>Emergency Medicine</option>
                <option value="Oral Health" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Oral Health' ? 'selected' : '' ?>>Oral Health</option>
                <option value="Laboratory Services" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Laboratory Services' ? 'selected' : '' ?>>Laboratory Services</option>
                <option value="General Services" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'General Services' ? 'selected' : '' ?>>General Services</option>
                <option value="Domestic Services" <?= isset($step_data['directorate']) && $step_data['directorate'] == 'Domestic Services' ? 'selected' : '' ?>>Domestic Services</option>
            </select>
        </div>
        
        <div id="knust_college_section" class="mb-3" style="display: none;">
            <label for="college" class="form-label">College/Department of PI if KNUST</label>
            <select id="college" name="college" class="form-select">
                <option value="">-- Select College --</option>
                <option value="College of Health Sciences" <?= isset($step_data['college']) && $step_data['college'] == 'College of Health Sciences' ? 'selected' : '' ?>>College of Health Sciences</option>
                <option value="College of Science" <?= isset($step_data['college']) && $step_data['college'] == 'College of Science' ? 'selected' : '' ?>>College of Science</option>
                <option value="College of Engineering" <?= isset($step_data['college']) && $step_data['college'] == 'College of Engineering' ? 'selected' : '' ?>>College of Engineering</option>
                <option value="College of Humanities and Social Sciences" <?= isset($step_data['college']) && $step_data['college'] == 'College of Humanities and Social Sciences' ? 'selected' : '' ?>>College of Humanities and Social Sciences</option>
                <option value="College of Agriculture and Natural Resources" <?= isset($step_data['college']) && $step_data['college'] == 'College of Agriculture and Natural Resources' ? 'selected' : '' ?>>College of Agriculture and Natural Resources</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="local_collab_details" class="form-label">Local Collaborator Details</label>
            <textarea class="form-control" id="local_collab_details" name="local_collab_details" rows="3"
                     placeholder="Enter details of local collaborators if applicable"><?= htmlspecialchars($step_data['local_collab_details'] ?? '') ?></textarea>
            <div class="form-text">If you are working with local collaborators, please provide their names, affiliations, and roles in the study.</div>
        </div>
        
        <div class="mb-3">
            <label for="supervisor_details" class="form-label">Supervisor Details for Student Projects</label>
            <textarea class="form-control" id="supervisor_details" name="supervisor_details" rows="3"
                     placeholder="Enter supervisor details if this is a student project"><?= htmlspecialchars($step_data['supervisor_details'] ?? '') ?></textarea>
            <div class="form-text">For student projects, provide the name, title, contact information, and affiliation of your supervisor.</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle nationality details
    const nationalityOther = document.getElementById('nationality_other');
    const nationalityDetails = document.getElementById('nationality_details');
    
    function toggleNationalityDetails() {
        nationalityDetails.style.display = nationalityOther.checked ? 'block' : 'none';
        
        // Add/remove required attribute
        const nationalitySpecific = document.getElementById('nationality_specific');
        if (nationalityOther.checked) {
            nationalitySpecific.setAttribute('required', 'required');
        } else {
            nationalitySpecific.removeAttribute('required');
        }
    }
    
    // Institution specific sections
    const institutionKath = document.getElementById('institution_kath');
    const institutionKnust = document.getElementById('institution_knust');
    const institutionOther = document.getElementById('institution_other');
    const kathDirectorateSection = document.getElementById('kath_directorate_section');
    const knustCollegeSection = document.getElementById('knust_college_section');
    const institutionOtherDetails = document.getElementById('institution_other_details');
    
    function toggleInstitutionSections() {
        kathDirectorateSection.style.display = institutionKath.checked ? 'block' : 'none';
        knustCollegeSection.style.display = institutionKnust.checked ? 'block' : 'none';
        institutionOtherDetails.style.display = institutionOther.checked ? 'block' : 'none';
        
        // Add/remove required attributes
        const directorate = document.getElementById('directorate');
        const college = document.getElementById('college');
        const institutionOtherSpecific = document.getElementById('institution_other_specific');
        
        directorate.required = institutionKath.checked;
        college.required = institutionKnust.checked;
        institutionOtherSpecific.required = institutionOther.checked;
    }
    
    // Set initial states
    toggleNationalityDetails();
    toggleInstitutionSections();
    
    // Add event listeners
    nationalityOther.addEventListener('change', toggleNationalityDetails);
    document.getElementById('nationality_ghanaian').addEventListener('change', toggleNationalityDetails);
    
    institutionKath.addEventListener('change', toggleInstitutionSections);
    institutionKnust.addEventListener('change', toggleInstitutionSections);
    institutionOther.addEventListener('change', toggleInstitutionSections);
});
</script>
