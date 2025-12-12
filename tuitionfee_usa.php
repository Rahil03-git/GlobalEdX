<?php
// Include the database connection file
include('connect.php');

// Define the query to fetch university details based on tuition fee categories for American universities
$query = "SELECT 
        u.name AS University_Name,
        u.global_rank AS World_Rank,
        GROUP_CONCAT(DISTINCT REPLACE(p.name, ' Programs', '') SEPARATOR ', ') AS Program_Type,
        tf.fee_category AS Tuition_Fee_Type,
        u.location AS Location
    FROM 
        University u
    INNER JOIN 
        UniversityProgramField upf ON u.university_id = upf.university_id
    INNER JOIN 
        Program p ON upf.program_id = p.program_id
    INNER JOIN 
        tuitionfee tf ON u.university_id = tf.university_id
    WHERE 
        u.country_id = (
            SELECT country_id 
            FROM CountryList 
            WHERE name = 'United States of America'
        )
        AND u.name != 'california institute of technology'
    GROUP BY 
        u.university_id, tf.fee_category
    ORDER BY 
        FIELD(tf.fee_category, 'Free', 'Medium', 'High')
";

// Execute the query
$result = $conn->query($query);
?>