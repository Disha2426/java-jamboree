<?php
// Database configuration (adjust if your credentials differ)
$servername = "localhost";
$username = "root";
$password = ""; // default for XAMPP
$dbname = "javajamboree"; // change this to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect and sanitize input
$user = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$pass = $_POST['password'];
$confirm_pass = $_POST['confirm_password'];

// Basic password confirmation
if ($pass !== $confirm_pass) {
    die("Passwords do not match!");
}

// Hash the password
$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

// Insert into database
$sql = "INSERT INTO user (U_Email_id, U_Password) VALUES ('$email', '$hashed_pass')";

if ($conn->query($sql) === TRUE) {
    echo "Registration successful!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
