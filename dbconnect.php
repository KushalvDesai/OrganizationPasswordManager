<?php
$host = '127.0.0.1';
$dbname = 'u149605981_passwdFINAL';
$username = 'u149605981_kushalvdesai';
$password = 'Catbox@2025';

$conn = new mysqli($host, $username, $password, $dbname, 3306);
if ($conn->connect_error) {
    die("Database connection failed.");
}
?>