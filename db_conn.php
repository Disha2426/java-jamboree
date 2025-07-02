<?php
$host = "localhost";
$username = "root";
$password = ""; // default in XAMPP
$dbname = "javajamboree"; 
$port = "3306";// change to your actual DB name

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>