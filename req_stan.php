<?php
// Include the database connection file
include('connect.php');

// Define the SQL query
$query = "SELECT 
        p.name AS Program_Name,
        f.name AS Field_Of_Study,
        ar.academic_qualification AS Academic_Qualification,
        ar.language_requirement AS Language_Requirement,
        ar.entrance_exam AS Entrance_Exam,
        ar.other_requirements AS Other_Requirements,
        ar.application_deadline AS Application_Deadline
    FROM 
        admissionrequirements ar
    INNER JOIN
        program p ON ar.program_id = p.program_id
    INNER JOIN 
        FieldOfStudy f ON ar.field_id = f.field_id
    INNER JOIN 
        university u ON ar.university_id = u.university_id
    INNER JOIN 
        countrylist c ON u.country_id = c.country_id
    WHERE
        c.name = 'United States of America'
        AND u.name = 'Stanford University'
    ORDER BY 
        p.name, f.name";

$result = $conn->query($query);

// Group data by program
$groupedData = [];
while ($row = $result->fetch_assoc()) {
    $groupedData[$row['Program_Name']][] = $row;
}
?>

<!-- // Execute the query
$result = $conn->query($query);
?> -->