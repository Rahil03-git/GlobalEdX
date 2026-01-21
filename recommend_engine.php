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