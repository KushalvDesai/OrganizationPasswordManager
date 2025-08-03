<?php
session_start();
require_once '../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data safely
    $appName = $_POST['appname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $cid = $_SESSION['cid'] ?? null;
    $access = isset($_POST['access']) ? 1 : 0;

    if (!$cid) {
        die("CID not set in session.");
    }

    // Prepare first insert for `passwd`
    $stmt1 = $conn->prepare("INSERT INTO passwd (`APP-NAME`, `USERNAME`, `PASS`, `CID`, `ACCESS`) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssii", $appName, $username, $password, $cid, $access);

    // Prepare second insert for `passwd_master`
    $stmt2 = $conn->prepare("INSERT INTO passwd_master (`APP-NAME`, `USERNAME`, `PASS`, `CID`, `ACCESS`) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssii", $appName, $username, $password, $cid, $access);

    // Execute both statements
    if ($stmt1->execute() && $stmt2->execute()) {
        header("Location: index.php?success=Password added successfully");
    } else {
        header("Location: index.php?error=Failed to add password");
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit;
}
?>
