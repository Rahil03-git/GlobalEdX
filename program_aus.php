<?php
// Include the database connection file
include('connect.php');

// Define the query to fetch program-related details for Australian universities
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
            WHERE name = 'Australia'
        )
        AND u.name != 'University of Queensland'
    GROUP BY 
        u.university_id
    ORDER BY 
        Program_Count DESC, u.global_rank
";

// Execute the query
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GlobalEdX</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-5EH+HLwMxaZJzDdMWNxlWfOmkR1F7xhEMBkCtKHxdsOTbPdCoP0U8yEFQRwrb5Pg" crossorigin="anonymous">
	<link rel="stylesheet" href="css/fontawesome.min.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet"  href="css/bootstrap.min.css">
	<link rel="stylesheet"  href="css/desh.css">
	<link rel="stylesheet"  href="css/responsive.css">
