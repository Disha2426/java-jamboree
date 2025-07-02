<?php
include 'db_conn.php';

$user_email = $_POST['user_email'];
$test_id = $_POST['test_id'];
$score = $_POST['score'];
$total = $_POST['total'];
$time = $_POST['time'];

$stmt = $conn->prepare("INSERT INTO quiz_attempts (user_email, test_id, score, total_questions, time_taken) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("siiis", $user_email, $test_id, $score, $total, $time);
$stmt->execute();
$stmt->close();
?>
