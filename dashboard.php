<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
<style>

<link rel="stylesheet" href="first.css">

</style>
</head>
<body>
  <h1>Welcome, <?php echo $_SESSION['user']; ?>!</h1>
  <p>You have successfully logged in.</p>
  <a href="logout.php">Logout</a>
</body>
</html>
