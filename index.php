<?php
session_start();
$isLoggedIn = isset($_SESSION['UserID']);
if ($isLoggedIn && !isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'Rahil'; // or get from database
    $_SESSION['email'] = 'rahil@example.com';
}

// Optional: provide a way to fetch session info via AJAX
if (isset($_GET['action']) && $_GET['action'] === 'status') {
    header('Content-Type: application/json');
    echo json_encode([
        'isLoggedIn' => $isLoggedIn,
        'user_name' => $_SESSION['user_name'] ?? null,
        'email' => $_SESSION['email'] ?? null
    ]);
    exit;
}
?>

<script>
async function checkLogin() {
    try {
        const response = await fetch('backend.php?action=status');
        const data = await response.json();
        if (data.isLoggedIn) {
            // Example: unlock buttons
            document.querySelectorAll('.locked').forEach(btn => {
                btn.classList.remove('locked');
                btn.classList.add('unlocked');
                // You can also update href dynamically if needed
            });
        }
    } catch (error) {
        console.error('Error fetching login status:', error);
    }
}

// Call on page load
document.addEventListener('DOMContentLoaded', checkLogin);
</script>
