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