<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../dbconnect.php';

echo '<pre>';
var_dump($_SESSION);
echo '</pre>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $appName = $_POST['appname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cid = $_SESSION['cid']; // Retrieve CID from session


    // Insert into `passwd`
    $stmt1 = $conn->prepare("INSERT INTO passwd (`APP-NAME`, `USERNAME`, `PASS`, `CID`) VALUES (?, ?, ?, ?)");
    $stmt1->bind_param("sssi", $appName, $username, $password, $cid);

    // Insert into `passwd_master`
    $stmt2 = $conn->prepare("INSERT INTO passwd_master (`APP-NAME`, `USERNAME`, `PASS`, `CID`) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("sssi", $appName, $username, $password, $cid);

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
