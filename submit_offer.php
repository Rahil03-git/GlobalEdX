<?php
include 'connect.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "globaledx";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='error-box'>Database connection failed: " . $conn->connect_error . "</div>");
}

date_default_timezone_set('Asia/Dhaka');


function uploadFile($fieldName, $fullName, $required = true) {
    if (!isset($_FILES[$fieldName])) {
        if ($required) die("<div class='error-box'>File upload error: $fieldName missing.</div>");
        return null;
    }

    $file = $_FILES[$fieldName];
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) die("<div class='error-box'>Required file '$fieldName' not uploaded.</div>");
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("<div class='error-box'>Upload error on '$fieldName': " . $file['error'] . "</div>");
    }

    $uploadDir = 'Offer Letter/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $safeName = preg_replace("/[^A-Za-z0-9]/", "", $fullName);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg','jpeg','png','pdf'];

    if (!in_array($ext, $allowedExts)) {
        die("<div class='error-box'>Invalid file type for $fieldName. Allowed: jpg, jpeg, png, pdf</div>");
    }

    $filename = $uploadDir . $safeName . "_" . $fieldName . "." . $ext;

    if (!move_uploaded_file($file['tmp_name'], $filename)) {
        die("<div class='error-box'>Failed to move uploaded file for $fieldName</div>");
    }

    return $filename;
}

$full_name = trim($_POST['full_name']);
$father_name = trim($_POST['father_name']);
$mother_name = trim($_POST['mother_name']);
$dob = $_POST['dob'];
$nationality = trim($_POST['nationality']);
$religion = trim($_POST['religion']);
$marital_status = trim($_POST['marital_status']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$present_address = trim($_POST['present_address']);
$ssc_year = $_POST['ssc_year'];
$hsc_year = $_POST['hsc_year'];
$medium = $_POST['medium'];
$country_of_choice = trim($_POST['country_of_choice']);
$preferred_university = trim($_POST['preferred_university']);
$uniStmt = $conn->prepare("SELECT name FROM university WHERE university_id = ?");
$uniStmt->bind_param("i", $preferred_university);
$uniStmt->execute();
$uniStmt->bind_result($university_name);
$uniStmt->fetch();
$uniStmt->close();
/* ✅ PROGRAM TYPE FIX (THIS WAS MISSING) */
$program_type = isset($_POST['program_type']) ? trim($_POST['program_type']) : '';

$programMap = [
    '1' => 'Undergraduate',
    '2' => 'Graduate',
    '3' => 'PhD'
];

$program_name = $programMap[$program_type] ?? '';
$preferred_subject = trim($_POST['preferred_subject']);
$intake_season = $_POST['intake_season'];
$intake_year = $_POST['intake_year'];
$already_applied = $_POST['already_applied'];

$nid_card_path = uploadFile('nid_card', $full_name, true);
$ssc_certificate_path = uploadFile('ssc_certificate', $full_name, true);
$hsc_certificate_path = uploadFile('hsc_certificate', $full_name, true);
$passport_scan_path = uploadFile('passport_scan', $full_name, false);
$english_cert_path = uploadFile('english_cert', $full_name, true);
$father_nid_path = uploadFile('father_nid', $full_name, true);
$mother_nid_path = uploadFile('mother_nid', $full_name, true);
$lor_path = uploadFile('lor', $full_name, true);
$sop_path = uploadFile('sop', $full_name, false);
$cv_path = uploadFile('cv', $full_name, false);
$passport_photo_path = uploadFile('passport_photo', $full_name, true);

$sql = "INSERT INTO offer_letter_requests 
(full_name, father_name, mother_name, date_of_birth, nationality, religion, marital_status, phone_number, email, present_address,
ssc_year, hsc_year, medium_of_study, nid_card_path, ssc_certificate_path, hsc_certificate_path, passport_scan_path,
english_cert_path, father_nid_path, mother_nid_path, lor_path, sop_path, cv_path, passport_photo_path,
country_of_choice, preferred_university, program_type, preferred_subject, intake_season, intake_year, already_applied, submitted_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssssssssssssssssssssssssssssss",
    $full_name, $father_name, $mother_name, $dob, $nationality, $religion, $marital_status, $phone, $email, $present_address,
    $ssc_year, $hsc_year, $medium, $nid_card_path, $ssc_certificate_path, $hsc_certificate_path, $passport_scan_path,
    $english_cert_path, $father_nid_path, $mother_nid_path, $lor_path, $sop_path, $cv_path, $passport_photo_path,
    $country_of_choice, $preferred_university, $program_type, $preferred_subject, $intake_season, $intake_year, $already_applied
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Offer Letter Submission</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

body{
  margin:0;
  background:#0b1220;
  font-family:'Poppins',sans-serif;
  display:flex;
  justify-content:center;
  align-items:center;
  min-height:100vh;
}

/* Wrapper */
.wrapper{
  width:100%;
  max-width:860px;
  background:#5A7ACD;
  border:1px solid rgba(255,255,255,.12);
  border-radius:26px;
  padding:30px 32px;
  box-shadow:0 35px 70px rgba(0,0,0,.55);
  color:#e5e7eb;
}

/* Header */
.top-status{
  display:flex;
  gap:14px;
  align-items:center;
  margin-bottom:8px;
}

.status-icon{
  width:60px;
  height:60px;
  border-radius:18px;
  background:linear-gradient(135deg,#34d399,#10b981);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:32px;
  color:#022c22;
}

.title{
  font-size:22px;
  font-weight:700;
}

.subtitle{
  color:#9ca3af;
  margin-top:2px;
}

/* Divider */
.line{
  height:1px;
  background:rgba(255,255,255,.12);
  margin:18px 0;
}

/* Info box */
.info{
  padding:12px 14px;
  border-radius:14px;
  background:#0b1220;
  border:1px dashed rgba(34,197,94,.45);
  color:#a7f3d0;
  font-size:14px;
}

/* Section header */
.section{
  margin-top:18px;
  font-weight:600;
  color:#cbd5e1;
}

/* Email preview */
.preview-card{
  margin-top:10px;
  font-family: 'Times New Roman', Times, serif;
  padding:14px;
  border-radius:14px;
  background:#fff;
  border:1px solid rgba(0, 0, 0, 0.95);
  white-space:pre-line;
  color:#0b0f1a;
}

/* Buttons area */
.btn-row{
  margin-top:18px;
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.btn{
  flex:1;
  padding:10px 16px;
  border-radius:12px;
  text-decoration:none;
  text-align:center;
  font-weight:600;
  border:1px solid rgba(255,255,255,.14);
  color:#e5e7eb;
  background:#360185;
  transition:.25s;
}
.btn:hover{filter:brightness(1.18);}

.primary{
  border-color:#4f46e5;
  background:linear-gradient(145deg,#4f46e5,#6366f1);
  color:#fff;
}

/* Loading */
.load-box{
  margin-top:14px;
  padding:14px;
  border-radius:14px;
  border:1px solid rgba(255,255,255,.15);
  background:#0b0f1a;
  text-align:center;
}

.spinner{
  width:36px;
  height:36px;
  border-radius:50%;
  border:3px solid rgba(255,255,255,.18);
  border-top:3px solid #818cf8;
  margin:6px auto;
  animation:spin 1s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}

.timer{
  margin-top:4px;
  color:#c7d2fe;
}

/* Error */
.error{
  border:1px solid rgba(239,68,68,.5);
  background:rgba(239,68,68,.1);
  color:#fecaca;
  padding:12px;
  border-radius:12px;
}
</style>
</head>

<body>

<div class="wrapper">

<?php if ($stmt->execute()): ?>

  <div class="top-status">
    <div class="status-icon">✔</div>

    <div>
      <div class="title">Application Submitted</div>
      <div class="subtitle">We’ve received your offer-letter request</div>
    </div>
  </div>

  <div class="info">
    Thanks <b><?= htmlspecialchars($full_name) ?></b> — your documents were uploaded
    successfully and will be reviewed within 24 hours.
  </div>

  <div class="line"></div>

<div class="section" id="emailStatusText">Generating university email draft…</div>

  <!-- Loading -->
  <div id="loadingBox" class="load-box">
    <div class="spinner"></div>
    <div class="timer">Please wait… <span id="countTimer">6</span>s</div>
  </div>

  <!-- Email Preview -->
  <div id="emailDialog" style="display:none;">

    <div class="section">Offer Letter Application Email</div>

<div class="preview-card" id="generatedEmail">
<?= htmlspecialchars(
    "Date: " . date('F j, Y') . "

To: Admissions Office
$university_name

Subject: Offer Letter Application for $program_name Program ($intake_season - $intake_year).

Dear Admission Committee,

I, $full_name, want to apply at $university_name in the subject of \"$preferred_subject\" which is from $program_name program, in the upcoming intake of $intake_season $intake_year.

All academic and personal documents have been submitted through the GlobalEdX application portal. Kindly review my profile and oblige me by providing the Official Offer Letter.

Thank you for your support. Waiting for your response.

Sincerely,
$full_name
Phone: $phone
Email: $email"
); ?>
</div>



    <div class="btn-row">

      <a href="mailto:?subject=Offer Letter Application&body=<?= urlencode("
$generatedEmail") ?>"
         class="btn primary">
         Send Email to University
      </a>

      <a href="index.php" class="btn">
        Return to Dashboard
      </a>

    </div>
  </div>

<script>
let t = 6;
const el = document.getElementById("countTimer");
const box = document.getElementById("loadingBox");
const dlg = document.getElementById("emailDialog");
const statusText = document.getElementById("emailStatusText");

const countdown = setInterval(() => {
    t--;
    el.textContent = t;

    if (t === 0) {
        clearInterval(countdown);

        box.style.display = "none";
        dlg.style.display = "block";

        // 🔹 Change text when email is ready
        statusText.textContent = "AI-generated email is ready.";
    }
}, 1000);
</script>

<?php else: ?>

  <div class="top-status">
    <div class="status-icon" style="background:rgba(239,68,68,.4);color:#450a0a;">✖</div>
    <div>
      <div class="title">Submission Failed</div>
      <div class="subtitle">Something went wrong</div>
    </div>
  </div>

  <div class="error">
    <?= htmlspecialchars($stmt->error) ?>
  </div>

  <div class="btn-row">
    <a href="offerletter.php" class="btn primary">Try Again</a>
  </div>

<?php endif; ?>

</div>

</body>
</html>
