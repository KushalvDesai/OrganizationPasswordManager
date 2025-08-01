<?php
session_start();

// Include the database connection file
require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING); // Assuming user_id is the email
    $password = trim($_POST['password']); // Trim to avoid whitespace issues

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT UID, CID, ROLE, PASSWORD FROM users WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($uid, $cid, $role, $hashed_password);

    if ($stmt->fetch()) {
        
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $uid;
            $_SESSION['role'] = $role;

            // Redirect to the vault animation page
            header("Location: vault_animation.php");
            exit;
        } else {
            // Invalid credentials
            header("Location: login.php?error=Invalid credentials");
            exit;
        }
    } else {
        // Invalid credentials
        header("Location: login.php?error=Invalid credentials");
        exit;
    }

    $stmt->close();
} else {
    header("Location: login.php");
    exit;
}

// Temporary debugging statement to output the hashed password for 'kushal06'
echo password_hash('kushal06', PASSWORD_BCRYPT);
exit;

$conn->close();
?>
