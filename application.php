<?php
include 'includes/header.php';
require 'config.php';
$prefill = $_SESSION['form_data'] ?? [];

// Ensure user has paid
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || $user['has_paid'] != 1) {
    header("Location: payment.php");
    exit();
}
?>

<!-- Internal CSS for form steps -->
<style>
    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
    }
</style>


<div class="container my-5">
    <h2 class="text-center mb-4">Research Application</h2>
    <form id="applicationForm" action="submit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="ref_code" value="<?php echo htmlspecialchars($prefill['ref_code'] ?? ''); ?>">

        <!-- Step 1 -->
        <div class="form-step active">
            <h4>Step 1: New Registration</h4>

            <div class="mb-3">
                <label for="purpose" class="form-label">Purpose of Study</label>
                <select class="form-select" id="purpose" name="purpose">
                    <option>Non-Degree</option>
                    <option>Diploma</option>
                    <option>1st Degree</option>
                    <option>PHD</option>
                    <option>Fellowship</option>
                    <option>Membership</option>
                    <option>2nd Degree</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Study Category</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="Sponsored by KATH">
                    <label class="form-check-label">Sponsored by KATH / Unit Budgets</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Sponsored by Local Ghanaian Organizations">
                    <label class="form-check-label">Sponsored by Local Ghanaian Organizations</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="With International Funding (US$ 15,000.00 - 500,000.00)">
                    <label class="form-check-label">With International Funding (US$ 15,000.00 - 500,000.00)</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="With International Funding (Less than US$ 15,000.00)">
                    <label class="form-check-label">With Internatioanl Funding (Less than US$ 15,000.00)</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="With Substantial International Grants/Contracts Research (Above US$ 500,000.00)">
                    <label class="form-check-label">With Substantial International Grants/Contracts Research (Above US$
                        500,000.00)</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Non-Ghanaian Inverstigators with no External Support">
                    <label class="form-check-label">Non-Ghanaian Inverstigators with no External Support</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Ghanaian Lecturers/ Professionals (Non KATH Employees without Funding">
                    <label class="form-check-label">Ghanaian Lecturers/ Professionals (Non KATH Employees without
                        Funding</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Ghanaian Lecturers / Professionals (KATH Employees without Funding">
                    <label class="form-check-label">Ghanaian Lecturers / Professionals (KATH Employees without
                        Funding</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Post Graduate Students with International Funding">
                    <label class="form-check-label">Post Graduate Students with International Funding</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Post Graduate Students with Local Funding">
                    <label class="form-check-label">Post Graduate Students with Local Funding</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Post Graduate Students (Without Funding)">
                    <label class="form-check-label">Post Graduate Students (Without Funding)</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="Undergraduate Students">
                    <label class="form-check-label">Undergraduate Students</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Non-Ghanaian Students with no External Support">
                    <label class="form-check-label">Non-Ghanaian Students with no External Support"</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category"
                        value="Ghanaian Lecturers / Professionals (With Funding less than US$ 5,000.00)">
                    <label class="form-check-label">Ghanaian Lecturers / Professionals (With Funding less than US$
                        5,000.00)</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="caseReport" value="Case Report">
                    <label class="form-check-label" for="caseReport">Case Report</label>
                </div><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="waivedOption" value="Study has been waived">
                    <label class="form-check-label" for="waivedOption">Study has been waived</label>
                </div><br>

                <!-- Waived Study reference number (initially hidden) -->
                <div id="waiverCodeInput" class="mt-2" style="display:none;">
                    <label for="waiverCode" class="form-label">Enter Waiver Reference Number</label>
                    <input type="text" name="waiver_code" id="waiverCode" class="form-control"
                        placeholder="Waiver Reference Number" required>
                </div><br>
            </div>

        </div>

        <!-- Step 2 PRINCIPAL INVESTIGATOR INFORMATION-->
        <div class="form-step">
            <h4>Step 2: Principal Investigator Info</h4>

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <select class="form-select" id="title" name="title">
                    <option value="Mr.">Mr.</option>
                    <option value="Mrs.">Mrs.</option>
                    <option value="Ms.">Ms.</option>
                    <option value="Dr.">Dr.</option>
                    <option value="Prof.">Prof.</option>
                    <option value="Rev.">Rev.</option>
                    <option value="Sir">Sir</option>
                    <option value="Miss">Miss</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control" id="surname" name="surname">
            </div>
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname">
            </div>
            <div class="mb-3">
                <label for="othernames" class="form-label">Other Name</label>
                <input type="text" class="form-control" id="othernames" name="othernames">
            </div>

            <div class="mb-3">
                <label class="form-label">Nationality</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="nationality" value="Ghanaian">
                    <label class="form-check-label">Ghanaian</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="nationality" value="Non-Ghanaian">
                    <label class="form-check-label">Other</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="localcollabDetails" class="form-label">Local Collaborator Details</label>
                <input type="text" class="form-control" id="localcollabDetails" name="localcollabDetails">
            </div>

            <div class="mb-3">
                <label for="supervisorDetailsforStudentprojects" class="form-label">Supervisor Details for Student
                    Projects</label>
                <input type="text" class="form-control" id="supervisorDetailsforStudentprojects"
                    name="supervisorDetailsforStudentprojects">
            </div>

            <div class="mb-3">
                <label for="KNUST" class="form-label">Principal Investigator Institution</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="KATH" name="KATH" value="KATH">
                    <label for="KATH" class="form-label">KATH</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="KNUST" name="KNUST" value="KNUST">
                    <label for="KNUST" class="form-label">KNUST</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="CCTH" name="CCTH" value="CCTH">
                    <label for="CCTH" class="form-label">CCTH</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="CCTH" name="CCTH" value="CCTH">
                    <label for="CCTH" class="form-label">CCTH</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="KBTH" name="KBTH" value="KBTH">
                    <label for="KBTH" class="form-label">KBTH</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="TTH" name="TTH" value="TTH">
                    <label for="TTH" class="form-label">TTH</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="other" name="other" value="other">
                    <label for="other" class="form-label">Other</label>
                </div>
            </div>

                <div class="mb-3">
                    <label for="directorate" class="form-label">Directorate/ Unit of Principal Investigator if
                        KATH</label>
                    <select id="directorate" name="directorate" class="form-select">
                        <option value="">-- Select Directorate --</option>
                        <option value="Quality Assurance">Quality Assurance</option>
                        <option value="Planning, Monitoring & Evaluation">Planning, Monitoring & Evaluation</option>
                        <option value="Public Affairs">Public Affairs</option>
                        <option value="Internal Audit">Internal Audit</option>
                        <option value="Human Resource Management">Human Resource Management</option>
                        <option value="Biostatics">Biostatics</option>
                        <option value="Supply Chain Management">Supply Chain Management</option>
                        <option value="Security">Security</option>
                        <option value="General Administration">General Administration</option>
                        <option value="Transfusion Medicine">Transfusion Medicine</option>
                        <option value="Public Health">Public Health</option>
                        <option value="Psychiatry">Psychiatry</option>
                        <option value="Research and Development">Research and Development</option>
                        <option value="Health Insurance">Health Insurance</option>
                        <option value="ICT">ICT</option>
                        <option value="Social Welfare">Social Welfare</option>
                        <option value="Chaplaincy">Chaplaincy</option>
                        <option value="Electric Medical Records System">Electric Medical Records System</option>
                        <option value="Project Management">Project Management</option>
                        <option value="Corporate and Special Services">Corporate and Special Services</option>
                        <option value="Obstetrics and Gynaecology">Obstetrics and Gynaecology</option>
                        <option value="Surgery">Surgery</option>
                        <option value="Child Health">Child Health</option>
                        <option value="Family Medicine">Family Medicine</option>
                        <option value="Anaesthesia and ICU">Anaesthesia and ICU</option>
                        <option value="EENT">Eye, Ear, Nose and Throat (EENT)</option>
                        <option value="Medicine">Medicine</option>
                        <option value="Radiology">Radiology</option>
                        <option value="Oncology">Oncology</option>
                        <option value="Trauma & Orthopaedics">Trauma & Orthopaedics</option>
                        <option value="Emergency Medicine">Emergency Medicine</option>
                        <option value="Oral Health">Oral Health</option>
                        <option value="Laboratory Services">Laboratory Services</option>
                        <option value="General Services">General Services</option>
                        <option value="Domestic Services">Domestic Services</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="college" class="form-label">College/Department of PI if KNUST</label>
                    <select id="college" name="college" class="form-select">
                        <option value="">-- Select College --</option>
                        <option value="College of Health Sciences">College of Health Sciences</option>
                        <option value="College of Science">College of Science</option>
                        <option value="College of Engineering">College of Engineering</option>
                        <option value="College of Humanities and Social Sciences">College of Humanities and Social
                            Sciences</option>
                        <option value="College of Agriculture and Natural Resources">College of Agriculture and Natural
                            Resources</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="institutioDepartment" class="form-label">Directorate/Department/Unit if PI's instution
                        is Other specify</label>
                    <input type="text" class="form-control" id="institutioDepartment" name="institutioDepartment">
                </div>

            </div>

        </div>

        <!-- Step 3 TRAINING AND CERTIFICATION-->
        <div class="form-step">
            <h4>Step 3: Research Training</h4>

            <!-- GCP Training -->
            <div class="mb-3">
                <label class="form-label">Have you had Good Clinical Practice (GCP) training in the past three (3) years?</label>
                <div>
                    <input type="radio" id="gcp_yes" name="gcp_training" value="Yes" onchange="toggleGCPDetails()"> Yes
                    <input type="radio" id="gcp_no" name="gcp_training" value="No" onchange="toggleGCPDetails()"> No
                    <input type="radio" id="gcp_na" name="gcp_training" value="N/A" onchange="toggleGCPDetails()"> N/A
                </div>
            </div>

            <div class="mb-3" id="gcp_details" style="display:none;">
                <label class="form-label">
                    If yes, state the name of place and dates of training; attach evidence of completion certificate.
                    <em>NB: GCP training for PI is mandatory for all proposals to conduct clinical trials</em>
                </label>
                <textarea class="form-control" name="gcp_details" rows="3"></textarea>
                <input type="file" name="gcp_certificate" class="form-control mt-2">
            </div>

            <!-- Research Ethics Training -->
            <div class="mb-3">
                <label class="form-label">Have you attended any research ethics training in the past three years?</label>
                <div>
                    <input type="radio" id="ethics_yes" name="ethics_training" value="Yes" onchange="toggleEthicsDetails()"> Yes
                    <input type="radio" id="ethics_no" name="ethics_training" value="No" onchange="toggleEthicsDetails()"> No
                </div>
            </div>

            <div class="mb-3" id="ethics_details" style="display:none;">
                <label class="form-label">
                    If yes, state the name of place and dates of training; attach evidence of completion certificate.
                </label>
                <textarea class="form-control" name="ethics_details" rows="3"></textarea>
                <input type="file" name="ethics_certificate" class="form-control mt-2">
            </div>

            <!-- GLP Training -->
            <div class="mb-3">
                <label class="form-label">Have you had Good Laboratory Practice (GLP) training in the past three (3) years?</label>
                <div>
                    <input type="radio" name="glp_training" value="Yes"> Yes
                    <input type="radio" name="glp_training" value="No"> No
                </div>
            </div>
        </div>


        <!-- Step 4 -->
        <div class="form-step">
            <h4>Step 4: Study Site</h4>

            <div class="mb-3">
                    <label for="study_site" class="form-label">Where do you intend to conduct the study?</label>
                    <select id="study_site" name="study_site" class="form-select">
                        <option value="">-- Select Directorate --</option>
                        <option value="Quality Assurance">Quality Assurance</option>
                        <option value="Planning, Monitoring & Evaluation">Planning, Monitoring & Evaluation</option>
                        <option value="Public Affairs">Public Affairs</option>
                        <option value="Internal Audit">Internal Audit</option>
                        <option value="Human Resource Management">Human Resource Management</option>
                        <option value="Biostatics">Biostatics</option>
                        <option value="Supply Chain Management">Supply Chain Management</option>
                        <option value="Security">Security</option>
                        <option value="General Administration">General Administration</option>
                        <option value="Transfusion Medicine">Transfusion Medicine</option>
                        <option value="Public Health">Public Health</option>
                        <option value="Psychiatry">Psychiatry</option>
                        <option value="Research and Development">Research and Development</option>
                        <option value="Health Insurance">Health Insurance</option>
                        <option value="ICT">ICT</option>
                        <option value="Social Welfare">Social Welfare</option>
                        <option value="Chaplaincy">Chaplaincy</option>
                        <option value="Electric Medical Records System">Electric Medical Records System</option>
                        <option value="Project Management">Project Management</option>
                        <option value="Corporate and Special Services">Corporate and Special Services</option>
                        <option value="Obstetrics and Gynaecology">Obstetrics and Gynaecology</option>
                        <option value="Surgery">Surgery</option>
                        <option value="Child Health">Child Health</option>
                        <option value="Family Medicine">Family Medicine</option>
                        <option value="Anaesthesia and ICU">Anaesthesia and ICU</option>
                        <option value="EENT">Eye, Ear, Nose and Throat (EENT)</option>
                        <option value="Medicine">Medicine</option>
                        <option value="Radiology">Radiology</option>
                        <option value="Oncology">Oncology</option>
                        <option value="Trauma & Orthopaedics">Trauma & Orthopaedics</option>
                        <option value="Emergency Medicine">Emergency Medicine</option>
                        <option value="Oral Health">Oral Health</option>
                        <option value="Laboratory Services">Laboratory Services</option>
                        <option value="General Services">General Services</option>
                        <option value="Domestic Services">Domestic Services</option>
                    </select>
                </div>

            <div class="mb-3">
                <label for="staffCategory" class="form-label">What category of staff will be involved in  your study?<br>Check all that applies.</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="Nurses" name="Nurses" value="Nurses">
                    <label for="Nurses" class="form-label">Nurses</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="Doctors" name="Doctors" value="Doctors">
                    <label for="Doctors" class="form-label">Doctors</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="Not Applicable" name="Not Applicable" value="Not Applicable">
                    <label for="Not Applicable" class="form-label">Not Applicable</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="other" name="other" value="other">
                    <label for="other" class="form-label">Other</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="researchEquipments" class="form-label">Based on your research proposal, which equipment will you use, List all </label>
                <input type="text" class="form-control" id="researchEquipments" name="researchEquipments">
            </div>

            <div class="mb-3">
                <label for="support" class="form-label">What physical or financial support do you anticipate to provide KATH during and after your study.</label>
                <input type="text" class="form-control" id="support" name="support">
            </div>
        </div>

        <!-- Step 5 -->
        <div class="form-step">
            <h4>Step 5: Study Design & Methodology</h4>

            <div class="mb-3">
                <label for="studyBackground" class="form-label">Study Background (include relevant African and / or Ghanaian Literature with references)</label>
                <textarea name="studyBackground" id="studyBackground" cols="30" rows="10" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="studyAimandObjectives" class="form-label">Study Aim and Objectives</label>
                <textarea name="studyAimandObjectives" id="studyAimandObjectives" cols="30" rows="10" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="conceptualFramework" class="form-label">Study Hypothesis or Conceptual framework</label>
                <textarea name="conceptualFramework" id="conceptualFramework" cols="30" rows="10" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="sampleSize" class="form-label">Sample Size</label>
                <input type="number" class="form-control" id="sampleSize" name="sampleSize" required>
            </div>

            <div class="mb-3">
                <label for="researchTitle" class="form-label">Research Title</label>
                <input type="text" class="form-control" id="researchTitle" name="researchTitle" required>
            </div>
        
            <div class="mb-3">
                <label for="keywords" class="form-label">Keywords (comma-separated)</label>
                <input type="text" class="form-control" id="keywords" name="keywords">
            </div>

            <div class="mb-3">
                <label for="submissionDate" class="form-label">Expected Submission Date</label>
                <input type="date" class="form-control" id="submissionDate" name="submissionDate">
            </div>
        </div>

        <!-- Step 8 -->
        <div class="form-step">
            <h4>Step 3: Research Details</h4>

            <div class="mb-3">
                <label for="researchTitle" class="form-label">Research Title</label>
                <input type="text" class="form-control" id="researchTitle" name="researchTitle" required>
            </div>
        
            <div class="mb-3">
                <label for="keywords" class="form-label">Keywords (comma-separated)</label>
                <input type="text" class="form-control" id="keywords" name="keywords">
            </div>

            <div class="mb-3">
                <label for="submissionDate" class="form-label">Expected Submission Date</label>
                <input type="date" class="form-control" id="submissionDate" name="submissionDate">
            </div>
        </div>

        <!-- Step 9 -->
        <div class="form-step">
            <h4>Step 4: Upload Supporting Documents</h4>

            <div class="mb-3">
                <label for="proposalFile" class="form-label">Proposal Document</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">Consent Form</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">MTA (if applicable)</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF, DOCX, JPG, PNG</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">CTA (if applicable)</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF, DOCX, JPG, PNG</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">DTA (if applicable)</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF, DOCX, JPG, PNG</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">FDA (if applicable)</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF, DOCX, JPG, PNG</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">Good Clinical Practice[GCP] Certificate (if applicable)</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF, DOCX, JPG, PNG</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">Good Laboratory Practice[GLP] Certificate (if applicable) </label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF, DOCX, JPG, PNG</small>
            </div>
            <div class="mb-3">
                <label for="proposalFile" class="form-label">Ethics Training Certificate (if applicable)</label>
                <input type="file" class="form-control" id="proposalFile" name="proposalFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png" required>
                <small class="text-muted">Accepted formats: PDF, DOCX, JPG, PNG</small>
            </div>

            <div class="mb-3">
                <label for="ethicsFile" class="form-label">Any Other Document Relevant for the Conduct of the Study (optional)</label>
                <input type="file" class="form-control" id="ethicsFile" name="ethicsFile"
                    accept=".pdf,.docx,.jpg,.jpeg,.png">
            </div>
        </div>

        <!-- Step 10 -->
        <div class="form-step">
            <h4>Step 5: Review Your Application</h4>

            <div class="border p-3 mb-3 bg-light">
                <p><strong>Purpose:</strong> <span id="reviewPurpose"></span></p>
                <p><strong>Category:</strong> <span id="reviewCategory"></span></p>
                <p><strong>Reference Number:</strong> <span id="reviewRefNumber"></span></p>
                <p><strong>Principal Investigator:</strong> <span id="reviewInvestigator"></span></p>
                <p><strong>Nationality:</strong> <span id="reviewNationality"></span></p>
                <p><strong>Research Title:</strong> <span id="reviewResearchTitle"></span></p>
                <p><strong>Keywords:</strong> <span id="reviewKeywords"></span></p>
                <p><strong>Expected Submission Date:</strong> <span id="reviewSubmissionDate"></span></p>
                <p><strong>Proposal File:</strong> Will be uploaded</p>
                <p><strong>Ethics Clearance:</strong> Optional</p>
            </div>


            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="confirmReview" required>
                <label class="form-check-label" for="confirmReview">I confirm that all information provided is accurate and complete to the best of my knowledge.</label>
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="confirmEthics" required>
                <label class="form-check-label" for="confirmEthics">Please note that making any false statement (s) for
                the purposes of securing R&D approval and conducting studies in KATH
                is prohibited. KATH reserves the right to suspend or stop your study
                if later it is found you have falsified information.</label>
            </div>

        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">Previous</button>
            <!-- <button type="button" id="saveDraftBtn" class="btn btn-secondary">Save & Resume Later</button> -->
            <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
            <button type="submit" class="btn btn-success" style="display:none;">Submit</button>
        </div>

    </form>
</div>

<script>
    // Show or hide waiver code input based on category selection When the DOM is ready
    document.addEventListener("DOMContentLoaded", function() {
        const waiverOption = document.getElementById("waivedOption");
        const waiverCodeInput = document.getElementById("waiverCodeInput");
        const allCategoryRadios = document.querySelectorAll('input[name="category"]');

        function toggleWaiverCode() {
            waiverCodeInput.style.display = waiverOption.checked ? "block" : "none";
        }

        allCategoryRadios.forEach(radio => {
            radio.addEventListener("change", toggleWaiverCode);
        });

        // Initial check (in case form is reloaded with selection)
        toggleWaiverCode();
    });

    function validateForm() {
        const waivedSelected = document.getElementById("waivedOption").checked;
        const waiverCode = document.getElementById("waiverCode").value.trim();

        if (waivedSelected && waiverCode === "") {
            alert("Please enter a waiver code since 'Study has been waived' is selected.");
            document.getElementById("waiverCode").focus();
            return false;
        }

        return true;
    }

    // Payment integration placeholder
    document.querySelectorAll('.category-option').forEach(option => {
        option.addEventListener('change', function() {
            const purpose = document.querySelector('select[name="purpose"]').value;
            const category = this.value;
            const waiverCode = document.querySelector('input[name="waiver_code"]').value;

            if (!category) return;

            // Save Step 1 data to sessionStorage
            sessionStorage.setItem('purpose', purpose);
            sessionStorage.setItem('category', category);
            sessionStorage.setItem('waiver_code', waiverCode);

            // Redirect to payment portal
            window.location.href = 'payment.php';
        });
    });

    // Load saved data from sessionStorage after payment
    document.addEventListener("DOMContentLoaded", () => {
        if (sessionStorage.getItem('paid')) {
            document.querySelector('select[name="purpose"]').value = sessionStorage.getItem('purpose');
            document.querySelector(`input[name="category"][value="${sessionStorage.getItem('category')}"]`).checked = true;
            document.querySelector('input[name="waiver_code"]').value = sessionStorage.getItem('waiver_code');

            // Show Step 2
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.add('active');

            // Optionally disable Step 1
            document.getElementById('step1').style.display = 'none';

            // Clear payment flag
            sessionStorage.removeItem('paid');
        }
    });

    // Multi-step form functionality
    const steps = document.querySelectorAll(".form-step");
    let currentStep = 0;

    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const submitBtn = document.querySelector("button[type='submit']");

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle("active", i === index);
        });
        prevBtn.style.display = index > 0 ? "inline-block" : "none";
        nextBtn.style.display = index < steps.length - 1 ? "inline-block" : "none";
        submitBtn.style.display = index === steps.length - 1 ? "inline-block" : "none";
    }

    prevBtn.onclick = () => {
        if (currentStep > 0) currentStep--;
        showStep(currentStep);
    };

    nextBtn.onclick = () => {
        if (currentStep < steps.length - 1) currentStep++;
        showStep(currentStep);
    };

    showStep(currentStep);
</script>
<script>
    const nextBtns = document.querySelectorAll('.btn-next');
    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Populate review fields before last step
            if (document.querySelectorAll('.form-step')[formStep].contains(document.getElementById(
                    'reviewPurpose'))) {
                document.getElementById('reviewPurpose').textContent = document.getElementById('purpose')
                    .value;
                document.getElementById('reviewCategory').textContent = document.getElementById('category')
                    .value;
                document.getElementById('reviewRefNumber').textContent = document.getElementById(
                    'refNumber').value;
                document.getElementById('reviewInvestigator').textContent = document.getElementById(
                    'investigatorName').value;
                document.getElementById('reviewNationality').textContent = document.getElementById(
                    'nationality').value;
                document.getElementById('reviewResearchTitle').textContent = document.getElementById(
                    'researchTitle').value;
                document.getElementById('reviewKeywords').textContent = document.getElementById('keywords')
                    .value;
                document.getElementById('reviewSubmissionDate').textContent = document.getElementById(
                    'submissionDate').value;
            }
        });
    });
</script>
<script>
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('multiStepForm'));
        fetch('save_progress.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert("Progress saved! Use this code to resume: " + data.ref_code);
                document.getElementById('refCodeInput').value = data.ref_code;
            });
    });

    // waived study toggle
    function toggleWaivedStudy() {
        const waivedStudy = document.getElementByName("category").value;
        const contentWrapper = document.getElementById("waivedStudyContentWrapper");

        if (waivedStudy === "Study has been waived") {
            contentWrapper.style.display = "block";
        } else {
            contentWrapper.style.display = "none";
        }
    }

    // Toggle GCP and Ethics details based on radio button selection
    function toggleGCPDetails() {
        const gcpYes = document.getElementById('gcp_yes').checked;
        document.getElementById('gcp_details').style.display = gcpYes ? 'block' : 'none';
    }

    function toggleEthicsDetails() {
        const ethicsYes = document.getElementById('ethics_yes').checked;
        document.getElementById('ethics_details').style.display = ethicsYes ? 'block' : 'none';
    }
</script>


<?php include 'includes/footer.php'; ?>