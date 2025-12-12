<?php
// Include the database connection file
include('connect.php');


// Prepare the query
$sql = "SELECT 
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
                WHERE name = 'Australia'
            )
            AND u.name != 'University of Queensland'
         GROUP BY 
           u.university_id, tf.fee_category
         ORDER BY 
           CAST(REGEXP_REPLACE(u.global_rank, '[^0-9]', '') AS UNSIGNED) ASC";

// Prepare the statement
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>