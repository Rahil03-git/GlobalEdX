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

// Collect POST data
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
$program_type = isset($_POST['program_type']) ? trim($_POST['program_type']) : '';
$preferred_subject = trim($_POST['preferred_subject']);
$intake_season = $_POST['intake_season'];
$intake_year = $_POST['intake_year'];
$already_applied = $_POST['already_applied'];

// Uploads
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
<title>Submit Offer Letter</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#667eea,#764ba2);
    margin:0;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#fff;
}
.container{
    background:rgba(255,255,255,0.1);
    padding:30px 40px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.3);
    max-width:550px;
    text-align:center;
    backdrop-filter:blur(10px);
}
.success{
    margin:18px 0;
    padding:14px;
    border-radius:8px;
    background:#28a745aa;
}
.btn{
    background:#fff;
    color:#764ba2;
    padding:10px 26px;
    border-radius:40px;
    text-decoration:none;
    display:inline-block;
    margin-top:18px;
}
.dialog-box{
    background:#fff;
    color:#333;
    border-radius:12px;
    padding:18px 20px;
    margin-top:18px;
    text-align:left;
    box-shadow:0 12px 28px rgba(0,0,0,.35);
    display:none;
}
.dialog-header{
    font-weight:600;
    margin-bottom:8px;
}
.loading-area{
    text-align:center;
}
.spinner{
    border:4px solid rgba(0,0,0,.15);
    border-top:4px solid #764ba2;
    border-radius:50%;
    width:48px;
    height:48px;
    margin:10px auto;
    animation:spin 1s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
.timer-text{font-weight:600;margin-top:6px}
.email-body{
    margin-top:12px;
    padding:12px;
    border-radius:8px;
    background:#f6f6ff;
    border:1px solid #ddd;
    white-space:pre-line;
}
</style>
</head>

<body>
<div class="container">

<?php if ($stmt->execute()): ?>

    <h1>Thank you, <?= htmlspecialchars($full_name) ?>!</h1>

    <div class="success">
        Your offer letter request has been submitted successfully.<br>
        Within 24 hours, we will send you an email.
    </div>

    <p><b>We have got all of your information!</b></p>
    <p>Below is the auto-generated offer letter email that will be sent to the university:</p>

    <!-- Loading Phase -->
    <div id="loadingBox" class="dialog-box" style="display:block;">
        <div class="loading-area">
            <div class="spinner"></div>
            <div class="timer-text">Generating email… <span id="countTimer">8</span>s</div>
        </div>
    </div>

    <!-- Email Preview Dialog -->
    <div id="emailDialog" class="dialog-box">

        <div class="dialog-header">Offer Letter Application Email</div>

        <div class="email-body" id="generatedEmail">
<?=
htmlspecialchars(
"To: Admissions Office
$preferred_university

Subject: Offer Letter Application – $preferred_subject ($intake_season $intake_year)

Dear Admission Team,

I, $full_name, am applying for admission to the $preferred_subject program
at $preferred_university for the $intake_season $intake_year intake.

All required academic and personal documents have been attached
as part of my application submission via GlobalEdX.

I kindly request you to review my profile
and issue my official Offer Letter.

Thank you for your time and kind support.

Sincerely,
$full_name
Phone: $phone
Email: $email"
)
?>
        </div>

        <a href="mailto:?subject=Offer Letter Application&body=<?= urlencode("
$generatedEmail") ?>" class="btn">
            Send Email to University
        </a>

    </div>

    <a href="index.php" class="btn">Back to home</a>

    <script>
        let timer = 3;
        const el = document.getElementById("countTimer");
        const loadingBox = document.getElementById("loadingBox");
        const emailDialog = document.getElementById("emailDialog");

        const countdown = setInterval(() => {
            timer--;
            el.textContent = timer;

            if (timer === 0) {
                clearInterval(countdown);
                loadingBox.style.display = "none";
                emailDialog.style.display = "block";
            }
        }, 1000);
    </script>

<?php else: ?>

    <h1>Oops! Something went wrong.</h1>
    <div class="error-box">
        Database insert error: <?= htmlspecialchars($stmt->error) ?>
    </div>
    <a href="offerletter.php" class="btn">Try Again</a>

<?php endif; ?>

</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
