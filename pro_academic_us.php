<?php
// Include the database connection file
include('connect.php');

// Define the SQL query with Field of Study added
$query = "SELECT 
    p.name AS Program_Name,
    s.name AS Subject_Name,
    f.name AS Field_Of_Study_Name,
    s.description AS Subject_Description
FROM 
    Program p
JOIN UniversityProgramField upf 
    ON p.program_id = upf.program_id
JOIN University u 
    ON upf.university_id = u.university_id
JOIN countrylist c 
    ON u.country_id = c.country_id
JOIN Subject s 
    ON upf.subject_id = s.subject_id
JOIN FieldOfStudy f
    ON s.field_id = f.field_id
WHERE 
    c.name = 'Australia'
    AND u.name = 'University of Sydney'
ORDER BY 
    p.name, s.name";

// Execute the query
$result = $conn->query($query);
?>
