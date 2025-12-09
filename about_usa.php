<?php
include('connect.php');  // Added semicolon at the end of this line

// SQL Query
$sql = "SELECT 
overview,
currency,
location,
social_status,
educational_status,
cultural_status,
living_style
FROM 
CountryDetails
WHERE 
country_id = (
    SELECT country_id 
    FROM CountryList 
    WHERE name = 'United States of America'
);

";

// Execute the query
$result = $conn->query($sql);
?>
