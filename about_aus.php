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
    WHERE name = 'Australia'
);

";

// Execute the query
$result = $conn->query($sql);
?>

// Execute the query
$result = $conn->query($sql);
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
  <style>
* {
  margin: 0;
  padding: 0;
  outline: 0;
}
a{
  text-decoration: none;
}
ul {
  list-style: none;
  padding: 0;
}

ul,ol{
  list-style: none;
}
h1,h2,h3,h4,h5,h6,p,nav,span,a,div,ul,li,footer{
padding: 0;
margin: 0;
}
/* header part css start */
header {
  background-color: #0C2132;
  padding: 8px;
  position: relative;
  transform-style: preserve-3d;
  animation: slideInHeader 1s ease-out forwards;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
}

/* 3D flip animation */
@keyframes slideInHeader {
  0% {
    transform: rotateX(-90deg);
    opacity: 0;
  }
  100% {
    transform: rotateX(0deg);
    opacity: 1;
  }
}

.welcome p {
  color: white;
  font-size: 1.1rem;
  font-weight: bold;
  margin: 0;
  animation: fadeInText 1.2s ease-out forwards;
}

@keyframes fadeInText {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.header_text {
  font-style: italic;
  float: right;
  color: #ffffff;
  font-size: 1.1rem;
  white-space: nowrap;
  overflow: hidden;
  /* caret removed: no border-right */
  width: 0;
  animation: typingLoop 8s steps(35, end) infinite;
}

/* Typing + Erasing Loop Animation */
@keyframes typingLoop {
  0% {
    width: 0;
  }
  40% {
    width: 400px;
  }
  60% {
    width: 400px;
  }
  100% {
    width: 0;
  }
}
/* header css part end */
