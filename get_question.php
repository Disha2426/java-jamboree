<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "";
$database = "javajamboree";
$port = "3306";

$conn = new mysqli($host, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$testId = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;

$sql = "SELECT * FROM question_bank WHERE Test_id = $testId LIMIT 10";
$result = $conn->query($sql);

$questions = [];

while ($row = $result->fetch_assoc()) {
    $correctIndex = array_search($row['Correct_Ans'], [
        $row['Option_1'], $row['Option_2'], $row['Option_3'], $row['Option_4']
    ]);

    $questions[] = [
        "question" => $row['Q_description'],
        "options" => [$row['Option_1'], $row['Option_2'], $row['Option_3'], $row['Option_4']],
        "correct" => $correctIndex !== false ? $correctIndex : 0
    ];
}

header('Content-Type: application/json');
echo json_encode($questions);
?>
