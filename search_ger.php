<?php
// Include the database connection file
include('connect.php');

// Check if search form was submitted and capture the search term
$searchTerm = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// Prepare SQL query to search for universities
$sql = "SELECT 
        u.name AS University_Name,
        u.global_rank AS World_Rank,
        GROUP_CONCAT(DISTINCT TRIM(TRAILING 's' FROM REPLACE(p.name, 'Program', '')) SEPARATOR ', ') AS Program_Type,
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
            WHERE name = 'Germany'
        )
    AND 
        (u.name LIKE ? OR u.name LIKE ? OR u.name LIKE ? OR u.name LIKE ?)
    GROUP BY u.university_id
";

// Prepare statement
$stmt = $conn->prepare($sql);

// Bind the search term to the query
$searchTermWithWildcard = "%" . $searchTerm . "%";
$stmt->bind_param('ssss', $searchTermWithWildcard, $searchTermWithWildcard, $searchTermWithWildcard, $searchTermWithWildcard);

// Execute query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

?>
