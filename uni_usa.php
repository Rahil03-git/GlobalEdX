<?php
// Include the database connection file
include('connect.php');

// Define the query to fetch universities for USA
$query = "SELECT 
        u.name AS University_Name,
        u.global_rank AS World_Rank,
        (SELECT GROUP_CONCAT(DISTINCT REPLACE(p.name, ' Programs', '') SEPARATOR ', ')  
         FROM UniversityProgramField upf
         JOIN Program p ON upf.program_id = p.program_id
         WHERE upf.university_id = u.university_id) AS Program_Type,
        (SELECT tf.fee_category 
         FROM TuitionFee tf 
         WHERE tf.university_id = u.university_id 
         LIMIT 1) AS Tuition_Fee_Type,
        u.location AS Location
    FROM 
        University u
    WHERE 
        u.country_id = (
            SELECT country_id 
            FROM CountryList 
            WHERE name = 'United States of America'
        )
        AND u.name != 'california institute of technology'
    ORDER BY
        RAND()
";



// Execute the query
$result = $conn->query($query);
?>
