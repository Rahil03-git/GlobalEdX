<?php
include 'connect.php';

/* ================= INPUT ================= */
$country_id    = $_POST['country_id'];
$university_id = $_POST['university_id'];
$program_id    = $_POST['program_id'];
$field_id      = $_POST['field_id'];
$subject_id    = $_POST['subject_id'];

$ssc   = isset($_POST['ssc_gpa']) ? floatval($_POST['ssc_gpa']) : null;
$hsc   = isset($_POST['hsc_gpa']) ? floatval($_POST['hsc_gpa']) : null;
$cgpa  = isset($_POST['cgpa'])    ? floatval($_POST['cgpa'])    : null;
$ielts = floatval($_POST['ielts']);
$exp   = isset($_POST['experience']) ? floatval($_POST['experience']) : 0;

/* 🌙 THEME */
$theme = isset($_POST['theme']) ? $_POST['theme'] : 'light';
$darkClass = ($theme === 'dark') ? 'dark' : '';

/* ================= FETCH SELECTED NAMES ================= */
$infoQ = mysqli_query($conn,"
  SELECT 
    u.name  AS university_name,
    p.name  AS program_name,
    f.name  AS field_name,
    s.name  AS subject_name
  FROM university u
  JOIN program p ON p.program_id = '$program_id'
  JOIN fieldofstudy f ON f.field_id = '$field_id'
  JOIN subject s ON s.subject_id = '$subject_id'
  WHERE u.university_id = '$university_id'
  LIMIT 1
");
$info = mysqli_fetch_assoc($infoQ);

/* ================= ELIGIBILITY CHECK ================= */
$eligible = true;

/* Undergraduate */
if ($program_id == 1) {
  if ($ssc < 3.50 || $hsc < 3.50 || $ielts < 5.5) $eligible = false;
}

/* Graduate */
if ($program_id == 2) {
  if ($hsc < 3.50 || $cgpa < 2.50 || $ielts < 5.5) $eligible = false;
}

/* PhD */
if ($program_id == 3) {
  if ($cgpa < 3.00 || $ielts < 5.5) $eligible = false;
}

/* ================= FETCH REQUIREMENTS ================= */
$reqQ = mysqli_query($conn,"
  SELECT * FROM admissionrequirements
  WHERE university_id='$university_id'
    AND program_id='$program_id'
    AND field_id='$field_id'
  LIMIT 1
");
$req = mysqli_fetch_assoc($reqQ);

/* ================= SCORING ================= */
$score = 0;

if ($eligible) {

  /* UNDERGRADUATE */
  if ($program_id == 1) {
    $score += ($ssc / 5) * 20;
    $score += ($hsc / 5) * 40;
    $score += ($ielts / 9) * 40;
  }

  /* GRADUATE */
  if ($program_id == 2) {
    $score += ($hsc / 5) * 10;
    $score += ($cgpa / 4) * 40;
    $score += ($ielts / 9) * 25;
    $score += min($exp,4) / 4 * 25;
  }

  /* PHD */
  if ($program_id == 3) {
    $score += ($cgpa / 4) * 30;
    $score += ($ielts / 9) * 20;
    $score += min($exp,6) / 6 * 50;
  }
}

/* ================= STATUS ================= */
if (!$eligible) {
  $status = "Not Eligible";
  $class = "low";
  $score = 0;
}
elseif ($score >= 75) {
  $status = "Highly Recommended";
  $class = "high";
}
elseif ($score >= 50) {
  $status = "Conditionally Recommended";
  $class = "medium";
}
else {
  $status = "Not Recommended";
  $class = "low";
}

$score = round($score);
?>

<!DOCTYPE html>
<html>
<head>
<title>Recommendation Result</title>

<style>
:root{
  --bg:#f4f6f8;
  --card:#ffffff;
  --text:#111827;
}
body.dark{
  --bg:#0f172a;
  --card:#020617;
  --text:#e5e7eb;
}

body{
  font-family:Arial;
  background:var(--bg);
  color:var(--text);
}

.container{
  width:900px;
  margin:40px auto;
  background:var(--card);
  padding:30px;
  border-radius:12px;
}

.status.high{color:#16a34a}
.status.medium{color:#f59e0b}
.status.low{color:#dc2626}

.progress{
  height:18px;
  background:#e5e7eb;
  border-radius:20px;
  margin:15px 0
}

.bar{height:100%}
.bar.high{background:#22c55e}
.bar.medium{background:#facc15}
.bar.low{background:#ef4444}

.meta{margin:10px 0;font-size:15px}

button{
  padding:12px 20px;
  background:#2563eb;
  color:#fff;
  border:none;
  border-radius:8px
}
</style>
</head>