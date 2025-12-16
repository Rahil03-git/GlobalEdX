<?php 
session_start();
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT UserID, PasswordHash, FullName FROM Users WHERE Email = ?");
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // DEBUGGING HELP
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['PasswordHash'])) {
            session_regenerate_id(true);
            $_SESSION['UserID'] = $row['UserID'];
            $_SESSION['user_name'] = $row['FullName']; // Store the name
            header("Location: index.php");
            exit;
        } else {
            $errorMessage = "Invalid password. Please try again.";
        }
    } else {
        $errorMessage = "No account found with this email.";
    }

    $stmt->close();
    $conn->close();
}
?>