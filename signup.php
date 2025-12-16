<?php 
session_start();
include('connect.php');

$errorMessage = '';
$signupSuccess = false;  // Initialize variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } elseif (strlen($password) < 3) {
        $errorMessage = "Password must be at least 3 characters long.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $checkStmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = "This email is already registered. Please log in or use a different email.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Users (FullName, Email, PasswordHash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullName, $email, $hashedPassword);
            if ($stmt->execute()) {
                $signupSuccess = true;
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                </script>";
            } else {
                $errorMessage = "Signup failed. Please try again.";
            }
            $stmt->close();
        }

        $checkStmt->close();
    }

    $conn->close();
}
?>