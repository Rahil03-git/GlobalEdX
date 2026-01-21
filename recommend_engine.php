<?php
include 'connect.php';

/* ================= DATA (UNCHANGED) ================= */
$countryResult = mysqli_query($conn,"
SELECT country_id,name FROM countrylist
WHERE name IN ('Australia','Germany','United States of America')
");

$universityResult = mysqli_query($conn,"
SELECT university_id,name,country_id FROM university
WHERE name IN (
'Australian National University','University of Melbourne','University of Sydney',
'Heidelberg University','Ludwig Maximilian University of Munich','Technical University of Munich',
'Harvard University','Massachusetts Institute of Technology','Stanford University'
)");

$programResult = mysqli_query($conn,"SELECT program_id,name FROM program");
$fieldResult   = mysqli_query($conn,"SELECT field_id,name FROM fieldofstudy");

$upfResult = mysqli_query($conn,"
SELECT upf.university_id,upf.program_id,upf.field_id,
s.subject_id,s.name subject_name
FROM universityprogramfield upf
JOIN subject s ON s.subject_id=upf.subject_id
");

/* ================= MAPS ================= */
$universitiesByCountry=[];
while($u=mysqli_fetch_assoc($universityResult)){
  $universitiesByCountry[$u['country_id']][]=$u;
}

$subjectsMap=[];
while($r=mysqli_fetch_assoc($upfResult)){
  $subjectsMap[$r['university_id']][$r['program_id']][$r['field_id']][]=[
    'id'=>$r['subject_id'],'name'=>$r['subject_name']
  ];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Recommendation Engine</title>

<style>
*{box-sizing:border-box}

:root{
  --bg:linear-gradient(135deg,#e0e7ff,#f8fafc);
  --card:#ffffff;
  --text:#1e293b;
  --border:#cbd5f5;
  --accent:#4f46e5;
}

body.dark{
  --bg:#0f172a;
  --card:#020617;
  --text:#e5e7eb;
  --border:#334155;
  --accent:#38bdf8;
}

body{
  margin:0;
  font-family:'Segoe UI',Tahoma,sans-serif;
  background:var(--bg);
  color:var(--text);
  transition:.3s;
}

.card{
  max-width:950px;
  margin:50px auto;
  background:var(--card);
  padding:35px 40px;
  border-radius:16px;
  box-shadow:0 25px 40px rgba(0,0,0,0.08);
}

h2{
  margin-top:0;
  margin-bottom:25px;
  text-align:center;
  font-size:28px;
  position:relative;
}

.dark-toggle{
  position:absolute;
  right:0;
  top:0;
  font-size:14px;
  cursor:pointer;
  color:var(--accent);
}

.section{
  margin-top:30px;
  margin-bottom:10px;
  font-size:16px;
  font-weight:600;
  border-bottom:1px solid #e5e7eb;
  padding-bottom:6px;
}

/* 🔧 SELECT FIX */
select,input{
  width:100%;
  padding:12px 14px;
  margin:8px 0;
  border-radius:8px;
  border:1px solid var(--border);
  font-size:14px;
  background:var(--card);
  color:var(--text);
  transition:all .2s ease;
}

/* 🔧 DROPDOWN OPTION FIX */
select option{
  background:#ffffff;
  color:#1e293b;
}

body.dark select{
  background:#020617;
  color:#e5e7eb;
  border-color:#334155;
}

body.dark select option{
  background:#020617;
  color:#e5e7eb;
}

/* 🔧 SAFETY: Hovered option */
body.dark select option:hover{
  background:#1e293b;
}

/* Focus */
select:focus,
input:focus{
  outline:none;
  border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(56,189,248,0.25);
}

button{
  margin-top:30px;
  width:100%;
  padding:15px;
  font-size:16px;
  background:linear-gradient(135deg,#4f46e5,#2563eb);
  color:#fff;
  border:none;
  border-radius:10px;
  cursor:pointer;
  font-weight:600;
}

button:hover{
  box-shadow:0 10px 20px rgba(37,99,235,.3);
}

.hidden{display:none}

.err{
  color:#dc2626;
  font-size:13px;
  margin-bottom:4px;
}

@media(max-width:768px){
  .card{margin:20px;padding:25px}
}
</style>
</head>

<body>
<div class="card">
<h2>
  🎯 Recommendation Engine
  <span class="dark-toggle" onclick="toggleDark()">🌙 Dark Mode</span>
</h2>

<form id="recForm" method="POST" action="submission.php">

<select name="country_id" id="country" required>
<option value="">Select Country</option>
<?php while($c=mysqli_fetch_assoc($countryResult)): ?>
<option value="<?= $c['country_id'] ?>"><?= $c['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="university_id" id="university" required>
<option value="">Select University</option>
</select>

<select name="program_id" id="program" required>
<option value="">Select Program</option>
<?php while($p=mysqli_fetch_assoc($programResult)): ?>
<option value="<?= $p['program_id'] ?>"><?= $p['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="field_id" id="field" required>
<option value="">Select Field</option>
<?php while($f=mysqli_fetch_assoc($fieldResult)): ?>
<option value="<?= $f['field_id'] ?>"><?= $f['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="subject_id" id="subject" required>
<option value="">Select Subject</option>
</select>

<div class="section">Academic Information</div>

<input id="ssc" name="ssc_gpa" placeholder="SSC GPA (x.xx)">
<div id="sscErr" class="err"></div>

<input id="hsc" name="hsc_gpa" placeholder="HSC GPA (x.xx)">
<div id="hscErr" class="err"></div>

<input id="cgpa" name="cgpa" placeholder="CGPA (x.xx)" class="hidden">
<div id="cgpaErr" class="err"></div>

<input id="exp" name="experience" placeholder="Experience (Years)" class="hidden">
<div id="expErr" class="err"></div>

<input id="ielts" name="ielts" placeholder="IELTS Score">
<div id="ieltsErr" class="err"></div>

<button type="submit">🔍 Get Recommendation</button>

<input type="hidden" name="theme" id="theme" value="light">
</form>
</div>



<script>
/* 🌙 DARK MODE */
function toggleDark(){
  document.body.classList.toggle('dark');

  const theme = document.body.classList.contains('dark') ? 'dark' : 'light';
  document.getElementById('theme').value = theme;

  // persist theme
  localStorage.setItem('theme', theme);
}

/* 🔁 Restore theme on reload */
window.addEventListener('DOMContentLoaded', () => {
  const savedTheme = localStorage.getItem('theme') || 'light';

  if(savedTheme === 'dark'){
    document.body.classList.add('dark');
  }

  document.getElementById('theme').value = savedTheme;
});
</script>


<script>
const universitiesByCountry = <?= json_encode($universitiesByCountry) ?>;
const subjectsMap = <?= json_encode($subjectsMap) ?>;

const country=document.getElementById('country');
const university=document.getElementById('university');
const program=document.getElementById('program');
const field=document.getElementById('field');
const subject=document.getElementById('subject');

const ssc=document.getElementById('ssc');
const hsc=document.getElementById('hsc');
const cgpa=document.getElementById('cgpa');
const exp=document.getElementById('exp');
const ielts=document.getElementById('ielts');

const sscErr=document.getElementById('sscErr');
const hscErr=document.getElementById('hscErr');
const cgpaErr=document.getElementById('cgpaErr');
const expErr=document.getElementById('expErr');
const ieltsErr=document.getElementById('ieltsErr');

country.onchange=()=>{
  university.innerHTML='<option value="">Select University</option>';
  (universitiesByCountry[country.value]||[]).forEach(u=>{
    university.innerHTML+=`<option value="${u.university_id}">${u.name}</option>`;
  });
};

function updateSubjects(){
  subject.innerHTML='<option value="">Select Subject</option>';
  (((subjectsMap[university.value]||{})[program.value]||{})[field.value]||[])
  .forEach(s=>subject.innerHTML+=`<option value="${s.id}">${s.name}</option>`);
}

university.onchange=updateSubjects;
program.onchange=()=>{
  updateSubjects();
  ssc.classList.add('hidden');
  hsc.classList.add('hidden');
  cgpa.classList.add('hidden');
  exp.classList.add('hidden');

  if(program.value==1){ ssc.classList.remove('hidden'); hsc.classList.remove('hidden'); }
  if(program.value==2){ hsc.classList.remove('hidden'); cgpa.classList.remove('hidden'); exp.classList.remove('hidden'); }
  if(program.value==3){ cgpa.classList.remove('hidden'); exp.classList.remove('hidden'); }
};
field.onchange=updateSubjects;

/* ================= VALIDATION ================= */
document.getElementById('recForm').onsubmit=e=>{
  let ok=true;

  const dec5 = /^\d+\.\d{2}$/;   // ✅ FIXED
  const dec4 = /^\d+\.\d{2}$/;
  const ieltsVals=[2.5,3,3.5,4,4.5,5,5.5,6,6.5,7,7.5,8,8.5,9];

  sscErr.textContent=hscErr.textContent=cgpaErr.textContent=expErr.textContent=ieltsErr.textContent='';

if (!ssc.classList.contains('hidden')) {
  const val = parseFloat(ssc.value);

  if (!dec5.test(ssc.value)) {
    sscErr.textContent = 'Enter valid SSC GPA (e.g. 3.50)';
    ok = false;
  }
  else if (val > 5.00) {
    sscErr.textContent = 'SSC GPA must be within 5.00';
    ok = false;
  }
  else if (val < 3.50) {
    sscErr.textContent = 'SSC GPA below 3.50 is not eligible';
    ok = false;
  }
}

if (!hsc.classList.contains('hidden')) {
  const val = parseFloat(hsc.value);

  if (!dec5.test(hsc.value)) {
    hscErr.textContent = 'Enter valid HSC GPA (e.g. 3.50)';
    ok = false;
  }
  else if (val > 5.00) {
    hscErr.textContent = 'HSC GPA must be within 5.00';
    ok = false;
  }
  else if (val < 3.50) {
    hscErr.textContent = 'HSC GPA below 3.50 is not eligible';
    ok = false;
  }
}

if (!cgpa.classList.contains('hidden')) {
  const val = parseFloat(cgpa.value);

  if (!dec4.test(cgpa.value)) {
    cgpaErr.textContent = 'Enter valid CGPA (e.g. 2.75)';
    ok = false;
  }
  else if (val > 4.00) {
    cgpaErr.textContent = 'CGPA must be within 4.00';
    ok = false;
  }
  else if (val < 3.00) {
    cgpaErr.textContent = 'CGPA below 3.00 is not eligible';
    ok = false;
  }
}

if(!exp.classList.contains('hidden')){
  const max=program.value==2?4:6;
  if(exp.value<0||exp.value>max){ expErr.textContent=`Experience must be 0–${max} years`; ok=false; }
}

if(!ieltsVals.includes(parseFloat(ielts.value))){
  ieltsErr.textContent='Enter valid IELTS score'; ok=false;
}else if(parseFloat(ielts.value)<5.5){
  ieltsErr.textContent='IELTS below 5.5 is not eligible'; ok=false;
}

if(!ok) e.preventDefault();
};
</script>
</body>
</html>