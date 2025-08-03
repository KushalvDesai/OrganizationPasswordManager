<?php
session_start();
require_once '../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $appName = $_POST['appname'];
    $username = $_POST['username'];
    $password = $_POST['new_password'];
    $cid = $_SESSION['cid']; // Retrieve CID from session

    // Update the password in `passwd`
    $stmtUpdate = $conn->prepare("UPDATE passwd SET `PASS` = ?, `CID` = ? WHERE `APP-NAME` = ? AND `USERNAME` = ?");
    $stmtUpdate->bind_param("siss", $password, $cid, $appName, $username);

    // Insert into `passwd_master` as a log/audit trail
    $stmtInsert = $conn->prepare("INSERT INTO passwd_master (`APP-NAME`, `USERNAME`, `PASS`, `CID`) VALUES (?, ?, ?, ?)");
    $stmtInsert->bind_param("sssi", $appName, $username, $password, $cid);

    // Execute both
    $success = $stmtUpdate->execute() && $stmtInsert->execute();

    // Close statements
    $stmtUpdate->close();
    $stmtInsert->close();
    $conn->close();

    if ($success) {
        header("Location: index.php?success=Password updated successfully");
    } else {
        header("Location: index.php?error=Failed to update password");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}
