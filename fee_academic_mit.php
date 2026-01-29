<?php
// Include the database connection file
include('connect.php');

// Define the SQL query
$query = "SELECT 
    p.name AS Program_Name,
    f.name AS Field_Of_Study,
    tf.semesters_or_trimesters AS Semesters_Or_Trimesters,
    tf.duration AS Duration,
    tf.tuition_fee AS USD$,
    tf.semesters_or_trimesters_fee AS Semester_Or_Trimester_Fees,
    tf.fee_category AS Tuition_Fee_Category
FROM 
    tuitionfee tf
INNER JOIN 
    program p ON tf.program_id = p.program_id
INNER JOIN 
    FieldOfStudy f ON tf.field_id = f.field_id
INNER JOIN 
    University u ON tf.university_id = u.university_id
INNER JOIN 
    countrylist c ON u.country_id = c.country_id
WHERE 
    c.name = 'United States of America'
    AND u.name = 'Massachusetts Institute of Technology'
ORDER BY 
    p.name, f.name";

// Execute the query
$result = $conn->query($query);

// Group the results by Program Name
$groupedTuitionData = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $programName = $row['Program_Name'];
        $groupedTuitionData[$programName][] = $row;
    }
}
?>
