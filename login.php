<?php
session_start();
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header("Location: login.html?error=Email and password required");
        exit();
    }

    $stmt = $conn->prepare("SELECT U_Password FROM user WHERE U_Email_id = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['U_Password'])) {
            $_SESSION['user'] = $email;
            header("Location: quiz.php");
            exit();
        } else {
            header("Location: login.html?error=Incorrect password");
            exit();
        }
    } else {
        header("Location: login.html?error=User not found");
        exit();
    }
}
?>
