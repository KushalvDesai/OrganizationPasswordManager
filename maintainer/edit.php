<?php
session_start();
require_once '../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $appName = $_POST['appname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['new_password'] ?? '';
    $cid = $_SESSION['cid'] ?? null;

    if (!$appName || !$username || !$password || !$cid) {
        header("Location: index.php?error=Missing required fields");
        exit;
    }


    // UPDATE password in `passwd`
    $stmt1 = $conn->prepare("UPDATE passwd SET `PASS` = ? WHERE `APP-NAME` = ? AND `USERNAME` = ? AND `CID` = ?");
    $stmt1->bind_param("sssi", $password, $appName, $username, $cid);

    // INSERT password into `passwd_master` (for history/audit trail)
    $stmt2 = $conn->prepare("INSERT INTO passwd_master (`APP-NAME`, `USERNAME`, `PASS`, `CID`) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("sssi", $appName, $username, $password, $cid);

    if ($stmt1->execute() && $stmt2->execute()) {
        header("Location: index.php?success=Password updated successfully");
    } else {
        header("Location: index.php?error=Failed to update password");
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit;
}
?>
