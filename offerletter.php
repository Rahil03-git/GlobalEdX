<?php
include 'connect.php';

/* ================= FILTERED DATA ONLY ================= */

// Allowed countries
$allowedCountries = [
  'Australia',
  'Germany',
  'United States of America'
];

// Allowed universities per country
$allowedUniversities = [
  'Australian National University',
  'University of Melbourne',
  'University of Sydney',
  'Heidelberg University',
  'Ludwig Maximilian University of Munich',
  'Technical University of Munich',
  'Harvard University',
  'Massachusetts Institute of Technology',
  'Stanford University'
];

// Fetch countries
$countryResult = mysqli_query($conn, "
  SELECT country_id, name 
  FROM countrylist 
  WHERE name IN ('Australia','Germany','United States of America')
  ORDER BY name
");

// Fetch universities
$universityResult = mysqli_query($conn, "
  SELECT university_id, name, country_id 
  FROM university 
  WHERE name IN (
    'Australian National University',
    'University of Melbourne',
    'University of Sydney',
    'Heidelberg University',
    'Ludwig Maximilian University of Munich',
    'Technical University of Munich',
    'Harvard University',
    'Massachusetts Institute of Technology',
    'Stanford University'
  )
  ORDER BY name
");

/* ================= NEW: SUBJECT MAPPING ================= */
$upfResult = mysqli_query($conn, "
  SELECT upf.university_id, upf.program_id, s.subject_id, s.name
  FROM universityprogramfield upf
  JOIN subject s ON s.subject_id = upf.subject_id
");

$subjectMap = [];
while ($row = mysqli_fetch_assoc($upfResult)) {
  $subjectMap[$row['university_id']][$row['program_id']][] = $row['name'];
}

/* Build university map for JS */
$universitiesByCountry = [];
while ($u = mysqli_fetch_assoc($universityResult)) {
  $universitiesByCountry[$u['country_id']][] = [
    'id' => $u['university_id'],
    'name' => $u['name']
  ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Offer Letter Application</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;font-family:'Inter',sans-serif;}
body{ 
  margin:0; 
  background:#0f172a; 
  min-height:100vh; 
  display:flex; 
  justify-content:center; 
  padding:40px 12px; 
} 
/* Layout container */ 
.form-wrapper{ 
  width:100%; 
  max-width:1050px; 
} 
/* Header */ 
.page-header{ 
  margin-bottom:22px; 
  text-align:center; 
} 
.page-header h1{ 
  color:#e5e7eb; 
  margin:0; 
  font-size:30px; 
  font-weight:700; 
} 
.page-header p{ 
  margin-top:6px; 
  color:#9ca3af; 
  font-size:14px; 
} 
/* Card */ 
.form-card{ 
  background:#020617; 
  border:1px solid rgba(255,255,255,.08); 
  border-radius:18px; 
  box-shadow:0 18px 48px rgba(0,0,0,.45); 
  padding:26px 30px; width:100%; 
} 
/* Section titles */ 
.section-title{ 
  margin-top:24px; 
  margin-bottom:10px; 
  color:#cbd5e1; 
  font-weight:600; 
  font-size:15px; 
  letter-spacing:.2px; 
} 
/* Grid */ 
.grid{ 
  display:grid; 
  grid-template-columns:1fr 1fr; 
  gap:16px 18px; 
} 
.grid.full{
  grid-template-columns:1fr;
} 
@media(max-width:820px){ 
  .grid{grid-template-columns:1fr;} 
} 
/* Field */ 
label{ 
  color:#9ca3af; 
  font-size:13px; 
  margin-bottom:6px; 
  display:block; 
}
   input,select,textarea{ 
    width:100%; 
    padding:11px 13px; 
    background:#020617; 
    border:1px solid rgba(255,255,255,.15); 
    border-radius:10px; 
    color:#e5e7eb; 
    font-size:14px; 
    transition:.25s; 
  } 
  input:focus,select:focus,textarea:focus{ 
    outline:none; 
    border-color:#38bdf8; 
    box-shadow:0 0 0 2px rgba(56,189,248,.25); 
  } 
  /* File upload */ 
  input[type="file"]{ 
    padding:9px; 
    background:#020617; 
  }

/* Checkbox row */ 
.agree-box{ 
  display:flex; 
  gap:10px; 
  align-items:flex-start; 
  padding:12px; 
  margin-top:18px; 
  border:3px dashed rgba(255,255,255,.18); 
  border-radius:10px; 
  background:#020617; 
} 
.agree-box p{ 
  color:#cbd5e1; 
  margin:0; 
  font-size:15px; 
} 
.agree-box input{
  width:16px;
  height:16px;
  margin-top:2px;
}

.submit-btn{
  margin-top:22px;
  width:100%;
  padding:13px;
  border:none;
  border-radius:12px;
  font-size:15px;
  font-weight:700;
  background:linear-gradient(135deg,#38bdf8,#0ea5e9);
  color:#020617;
  cursor:pointer;
}
#quitBtn{
  position:fixed;
  top:18px;
  right:18px;
  padding:10px 16px;
  border:none;
  border-radius:10px;
  background:#ef4444;
  color:#fff;
  font-weight:600;
  cursor:pointer;
}

/* Submit Button Hover */
.submit-btn:hover{
  background:linear-gradient(135deg,#0ea5e9,#0284c7);
  box-shadow:0 8px 20px rgba(14,165,233,.35);
  transform:translateY(-1px);
}

/* Quit Button Hover */
#quitBtn:hover{
  background:#dc2626;
  box-shadow:0 6px 16px rgba(239,68,68,.4);
  transform:translateY(-1px);
}

/* Smooth transition for both */
.submit-btn,
#quitBtn{
  transition:all .25s ease;
}

/* Modal (unchanged logic — UI refreshed) */ #quitModal,#successModal{ position:fixed; top:0;left:0; width:100%;height:100%; background:rgba(0,0,0,.65); display:none; justify-content:center; align-items:center; z-index:9999; } .modal-box{ background:#020617; padding:24px 22px; border-radius:14px; border:1px solid rgba(255,255,255,.15); width:92%; max-width:420px; text-align:center; } .modal-box h2{color:#e5e7eb;margin-bottom:6px;} .modal-box p{color:#9ca3af;font-size:14px;margin-bottom:16px;} .modal-btn{ padding:10px 16px; border:none; border-radius:10px; font-weight:600; cursor:pointer; } .primary{background:#0ea5e9;color:#020617;} .secondary{background:#e5e7eb;color:#020617;} textarea.email-preview{ width:100%; height:220px; margin-top:10px; border-radius:10px; background:#020617; border:1px solid rgba(255,255,255,.18); color:#e5e7eb; padding:10px 12px; font-size:13px; } </style>
</head>

<body>

<div class="form-wrapper">

  <div class="page-header">
    <h1>Offer Letter Application Form</h1>
    <p>Please provide accurate information to proceed</p>
  </div>

  <form class="form-card" action="submit_offer.php" method="POST" enctype="multipart/form-data">

    <div class="section-title">Personal Information</div>
    <div class="grid">
      <div><label>Full Name</label><input type="text" name="full_name" required></div>
      <div><label>Father’s Name</label><input type="text" name="father_name" required></div>
      <div><label>Mother’s Name</label><input type="text" name="mother_name" required></div>
      <div><label>Date of Birth</label><input type="date" name="dob" required></div>
      <div><label>Nationality</label><input type="text" name="nationality" required></div>
      <div><label>Religion</label><input type="text" name="religion" required></div>
      <div><label>Marital Status</label><input type="text" name="marital_status" required></div>
    </div>

    <div class="section-title">Contact Details</div>
    <div class="grid">
      <div><label>Phone</label><input type="text" name="phone" required></div>
      <div><label>Email</label><input type="email" name="email" required></div>
    </div>

    <div class="grid full">
      <div><label>Present Address</label><textarea name="present_address" required></textarea></div>
    </div>

    <div class="section-title">Academic Information</div> <div class="grid"> <div> <label>SSC/O Levels Passing Year</label> <input type="text" name="ssc_year" required> </div> <div> <label>HSC/A Levels Passing Year</label> <input type="text" name="hsc_year" required> </div> <div> <label>Medium of Study</label> <select name="medium" required> <option value="English">English</option> <option value="Bangla">Bangla</option> </select> </div> </div> <div class="section-title">Document Uploads</div> <div class="grid"> <div><label>NID Card</label><input type="file" name="nid_card" required></div> <div><label>SSC Certificate</label><input type="file" name="ssc_certificate" required></div> <div><label>HSC Certificate</label><input type="file" name="hsc_certificate" required></div> <div><label>Passport (optional)</label><input type="file" name="passport_scan"></div> <div><label>English Proficiency</label><input type="file" name="english_cert" required></div> <div><label>Father NID</label><input type="file" name="father_nid" required></div> <div><label>Mother NID</label><input type="file" name="mother_nid" required></div> <div><label>Recommendation Letter</label><input type="file" name="lor" required></div> <div><label>Statement of Purpose (optional)</label><input type="file" name="sop"></div> <div><label>CV / Resume (optional)</label><input type="file" name="cv"></div> <div><label>Passport Photo</label><input type="file" name="passport_photo" required></div> </div>

    <div class="section-title">Application Preference</div>
    <div class="grid">

      <!-- COUNTRY -->
      <div>
        <label>Country Preference</label>
        <select name="country_of_choice" id="countrySelect" required>
          <option value="" disabled selected>Select Country</option>
          <?php while ($c = mysqli_fetch_assoc($countryResult)) { ?>
            <option value="<?= $c['country_id']; ?>">
              <?= htmlspecialchars($c['name']); ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <!-- UNIVERSITY -->
      <div>
        <label>University Preference</label>
        <select name="preferred_university" id="universitySelect" required disabled>
          <option>Select country first</option>
        </select>
      </div>

      <!-- PROGRAM TYPE -->
      <div>
        <label>Program Type</label>
        <select name="program_type" id="programSelect" required disabled>
          <option value="" disabled selected>Select university first</option>
          <option value="1">Undergraduate</option>
          <option value="2">Graduate</option>
          <option value="3">PhD</option>
        </select>
      </div>

      <!-- SUBJECT -->
      <div>
        <label>Preferred Subject</label>
        <select name="preferred_subject" id="subjectSelect" required disabled>
          <option value="" disabled selected>Select program first</option>
        </select>
      </div>

      <div>
        <label>Intake Session</label>
        <select name="intake_season" required>
          <option>Spring</option><option>Summer</option>
          <option>Fall</option><option>Winter</option>
        </select>
      </div>

      <div>
        <label>Intake Year</label>
        <select name="intake_year" required>
          <option>2024</option><option>2025</option><option>2026</option>
          <option>2027</option><option>2028</option><option>2029</option><option>2030</option>
        </select>
      </div>

      <div> <label>Already Applied?</label> <select name="already_applied" required> <option>Yes</option><option>No</option> </select> </div>

    </div>

    <div class="agree-box">
      <input type="checkbox" name="agree_data_use" required>
      <p>I agree to allow GlobalEdX to process my data.</p>
    </div>

    <button class="submit-btn" type="submit">Submit Application</button>

  </form>
</div>

<button id="quitBtn" type="button">Quit</button>


<script>
const universityMap = <?= json_encode($universitiesByCountry); ?>;
const subjectMap = <?= json_encode($subjectMap); ?>;

const country = document.getElementById('countrySelect');
const university = document.getElementById('universitySelect');
const program = document.getElementById('programSelect');
const subject = document.getElementById('subjectSelect');

/* COUNTRY → UNIVERSITY */
country.addEventListener('change', () => {
  // Reset university
  university.innerHTML = '<option value="" disabled selected>Select University</option>';
  university.disabled = false;

  // Reset program
  program.innerHTML = '<option value="" disabled selected>Select university first</option>';
  program.disabled = true;

  // Reset subject
  subject.innerHTML = '<option value="" disabled selected>Select program first</option>';
  subject.disabled = true;

  // Populate universities
  if (universityMap[country.value]) {
    universityMap[country.value].forEach(u => {
      const opt = document.createElement('option');
      opt.value = u.id;
      opt.textContent = u.name;
      university.appendChild(opt);
    });
  }
});

/* UNIVERSITY → PROGRAM */
university.addEventListener('change', () => {
  program.innerHTML = `
    <option value="" disabled selected>Select Program</option>
    <option value="1">Undergraduate</option>
    <option value="2">Graduate</option>
    <option value="3">PhD</option>
  `;
  program.disabled = false;

  // Reset subject
  subject.innerHTML = '<option value="" disabled selected>Select program first</option>';
  subject.disabled = true;
});

/* PROGRAM → SUBJECT */
program.addEventListener('change', () => {
  subject.innerHTML = '<option value="" disabled selected>Select Subject</option>';
  subject.disabled = false;

  const list = subjectMap[university.value]?.[program.value] || [];
  list.forEach(name => {
    const opt = document.createElement('option');
    opt.value = name;
    opt.textContent = name;
    subject.appendChild(opt);
  });
});

  document.getElementById("quitBtn").addEventListener("click", function () {
    window.location.href = "index.php";
  });

  
</script>


</body>
</html>
