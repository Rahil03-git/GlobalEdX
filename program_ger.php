<?php
// Include the database connection file
include('connect.php');

// Define the query to fetch program-related details for German universities
$query = "SELECT 
        u.name AS University_Name,
        u.global_rank AS World_Rank,
        GROUP_CONCAT(DISTINCT REPLACE(p.name, ' Programs', '') SEPARATOR ', ') AS Program_Type,
        COUNT(DISTINCT upf.program_id) AS Program_Count,
        u.location AS Location
    FROM 
        University u
    INNER JOIN 
        UniversityProgramField upf ON u.university_id = upf.university_id
    INNER JOIN 
        Program p ON upf.program_id = p.program_id
    WHERE 
        u.country_id = (
            SELECT country_id 
            FROM CountryList 
            WHERE name = 'Germany'
        )
        AND u.name != 'charité universitätsmedizin berlin'
    GROUP BY 
        u.university_id
    ORDER BY 
        Program_Count DESC, u.global_rank
";

// Execute the query
$result = $conn->query($query);
?>