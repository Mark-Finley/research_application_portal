<!-- application.php -->
<?php
session_start();
require 'config.php';

// Ensure user has paid
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || $user['has_paid'] != 1) {
    header("Location: payment.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
  <form id="researchForm" method="POST" enctype="multipart/form-data" action="actions/submit_application.php">
    
    <!-- Step 1: Registration -->
    <div class="form-step">
      <h5>Step 1: New Registration</h5>
      <label>Purpose of Study</label>
      <select name="purpose" class="form-control">
        <option>Academic</option>
        <option>General</option>
      </select>

      <label>Study Category</label>
      <div>
        <input type="radio" name="category" value="clinical"> Clinical Study
        <input type="radio" name="category" value="observational"> Observational Study
        <input type="radio" name="category" value="waived" id="waivedOption"> Study has been waived
      </div>

      <div id="waiverCodeDiv" style="display: none;">
        <label>Enter Waiver Code</label>
        <input type="text" name="waiver_code" class="form-control">
      </div>
    </div>

    <!-- Step 2: Principal Investigator -->
    <div class="form-step">
      <h5>Step 2: Principal Investigator</h5>
      <!-- Investigator info -->
      <input name="title" placeholder="Title" class="form-control mb-2">
      <input name="surname" placeholder="Surname" class="form-control mb-2">
      <input name="firstname" placeholder="First Name" class="form-control mb-2">
      <input name="othername" placeholder="Other Name" class="form-control mb-2">
      <label>Nationality</label>
      <input type="radio" name="nationality" value="Ghanaian"> Ghanaian
      <input type="radio" name="nationality" value="Other"> Other

      <textarea name="collaborator_details" placeholder="Local Collaborator Details" class="form-control mt-2"></textarea>
      <textarea name="supervisor_details" placeholder="Supervisor Details (if student)" class="form-control mt-2"></textarea>

      <label>Institution</label>
      <div>
        <input type="checkbox" name="institution[]" value="KATH"> KATH
        <input type="checkbox" name="institution[]" value="KNUST"> KNUST
        <input type="checkbox" name="institution[]" value="Other"> Other
      </div>

      <label>Department / Unit</label>
      <input type="text" name="department_unit" class="form-control">
    </div>

    <!-- Step 3: Research Training -->
    <div class="form-step">
      <h5>Step 3: Research Training</h5>
      <label>GCP Training (Last 3 Years)?</label>
      <input type="radio" name="gcp" value="yes"> Yes
      <input type="radio" name="gcp" value="no"> No
      <input type="file" name="gcp_file" class="form-control">

      <label>Ethics Training?</label>
      <input type="radio" name="ethics" value="yes"> Yes
      <input type="radio" name="ethics" value="no"> No
      <input type="file" name="ethics_file" class="form-control">
    </div>

    <!-- Step 4: Study Site -->
    <div class="form-step">
      <h5>Step 4: Study Site</h5>
      <label>Where is the study conducted?</label>
      <select name="study_site" class="form-control">
        <option>KATH</option>
        <option>Other</option>
      </select>

      <label>Staff Involved</label>
      <div>
        <input type="checkbox" name="staff[]" value="Nurses"> Nurses
        <input type="checkbox" name="staff[]" value="Doctors"> Doctors
        <input type="checkbox" name="staff[]" value="Not Applicable"> Not Applicable
      </div>

      <textarea name="equipment" placeholder="Research Equipment" class="form-control mt-2"></textarea>
      <textarea name="support" placeholder="Support to KATH" class="form-control mt-2"></textarea>
    </div>

    <!-- Step 5: Study Design & Methodology -->
    <div class="form-step">
      <h5>Step 5: Study Design & Methodology</h5>
      <textarea name="background" placeholder="Study Background" class="form-control"></textarea>
      <textarea name="aim_objectives" placeholder="Aim and Objectives" class="form-control mt-2"></textarea>
      <textarea name="hypothesis" placeholder="Hypothesis / Framework" class="form-control mt-2"></textarea>
      <input type="number" name="sample_size" placeholder="Sample Size" class="form-control mt-2">
      <input type="text" name="research_title" placeholder="Research Title" class="form-control mt-2">
      <input type="text" name="keywords" placeholder="Keywords" class="form-control mt-2">
      <input type="date" name="submission_date" class="form-control mt-2">
    </div>

    <!-- Step 6: Upload Supporting Documents -->
    <div class="form-step">
      <h5>Step 6: Upload Supporting Documents</h5>
      <label>Proposal</label>
      <input type="file" name="proposal_file" class="form-control">
      <label>Consent Form</label>
      <input type="file" name="consent_file" class="form-control">
      <label>Other Certificates (Ethics, FDA, GCP, etc)</label>
      <input type="file" name="other_documents[]" class="form-control" multiple>
    </div>

    <!-- Step 7: Review -->
    <div class="form-step">
      <h5>Step 7: Review Application</h5>
      <p>Ensure all details are filled. Tick to confirm:</p>
      <input type="checkbox" name="confirm_data" required> I confirm all the details provided are true and complete.
    </div>

    <!-- Step 8: Submit -->
    <div class="form-step">
      <h5>Step 8: Submit Application</h5>
      <button type="submit" class="btn btn-success">Submit Application</button>
    </div>

    <div class="mt-3">
      <button type="button" class="btn btn-secondary" id="prevBtn">Previous</button>
      <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
    </div>

  </form>
</div>

<?php include 'includes/footer.php'; ?>

<script>
let currentStep = 0;
const steps = document.querySelectorAll(".form-step");
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");

function showStep(step) {
  steps.forEach((s, i) => s.style.display = i === step ? "block" : "none");
  prevBtn.style.display = step === 0 ? "none" : "inline-block";
  nextBtn.style.display = step === steps.length - 1 ? "none" : "inline-block";
}

showStep(currentStep);

nextBtn.addEventListener("click", () => {
  if (currentStep < steps.length - 1) currentStep++;
  showStep(currentStep);
});
prevBtn.addEventListener("click", () => {
  if (currentStep > 0) currentStep--;
  showStep(currentStep);
});

// Waiver toggle
document.getElementById("waivedOption").addEventListener("change", function () {
  document.getElementById("waiverCodeDiv").style.display = this.checked ? "block" : "none";
});
</script>
