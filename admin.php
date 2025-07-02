<?php
session_start();
include 'db_conn.php';

// Admin credentials
$admin_email = "admin@example.com";
$admin_password = "admin123"; // In production, you should hash this password

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin'] = true;
        header('Location: admin.php'); // Redirect to admin panel if logged in
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Handle insert
if (isset($_POST['insert'])) {
    $test_id = intval($_POST['test_id']);
    $desc = $conn->real_escape_string($_POST['description']);
    $opt1 = $conn->real_escape_string($_POST['option1']);
    $opt2 = $conn->real_escape_string($_POST['option2']);
    $opt3 = $conn->real_escape_string($_POST['option3']);
    $opt4 = $conn->real_escape_string($_POST['option4']);
    $correct = $conn->real_escape_string($_POST['correct']);

    // Check if all required fields are filled
    if (empty($desc) || empty($opt1) || empty($opt2) || empty($opt3) || empty($opt4) || empty($correct)) {
        $insert_error = "All fields must be filled.";
    } else {
        // Prepare SQL query to insert the question
        $query = "INSERT INTO question_bank (Test_id, Q_description, Option_1, Option_2, Option_3, Option_4, Correct_Ans) 
                  VALUES ($test_id, '$desc', '$opt1', '$opt2', '$opt3', '$opt4', '$correct')";
        
        // Execute the query
        if ($conn->query($query) === TRUE) {
            $insert_success = "Question inserted successfully!";
        } else {
            // Capture error if query fails
            $insert_error = "Error: " . $conn->error;
        }
    }
}

// Handle delete
if (isset($_POST['delete'])) {
    $qid = intval($_POST['qid']);
    $conn->query("DELETE FROM question_bank WHERE Q_id = $qid");
}

// Fetch all questions from the database
$questions_query = "SELECT * FROM question_bank";
$questions_result = $conn->query($questions_query);

// Fetch total number of questions
$total_questions_query = "SELECT COUNT(*) AS total FROM question_bank";
$total_questions_result = $conn->query($total_questions_query);
$total_questions = $total_questions_result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f0f0; margin: 30px; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: auto; box-shadow: 0 0 10px #ccc; }
        input, textarea, select { width: 100%; margin: 8px 0; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px 20px; background: #2575fc; color: white; border: none; border-radius: 6px; cursor: pointer; }
        .logout-btn { background: red; float: right; }
        h2 { text-align: center; }
        .success { color: green; }
        .error { color: red; }
        .question-list { margin-top: 20px; }
        .question-item { background: #f9f9f9; padding: 10px; border-radius: 6px; margin-bottom: 10px; }
        .question-item button { background: red; color: white; border: none; cursor: pointer; padding: 5px 10px; border-radius: 6px; }
        .question-item button:hover { background: darkred; }
        .stats { margin-bottom: 20px; padding: 10px; background: #e6f7ff; border-radius: 6px; }
    </style>
</head>
<body>

<div class="container">
    <?php if (!isset($_SESSION['admin'])): ?>
        <h2>Admin Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Admin Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button name="login">Login</button>
        </form>
    <?php else: ?>
        <form method="post"><button name="logout" class="logout-btn">Logout</button></form>
        
        <div class="stats">
            <h3>Total Questions: <?= $total_questions ?></h3>
        </div>

        <h2>Insert Question</h2>
        <?php if (isset($insert_success)) echo "<p class='success'>$insert_success</p>"; ?>
        <?php if (isset($insert_error)) echo "<p class='error'>$insert_error</p>"; ?>
        <form method="post">
            <select name="test_id" required>
                <option value="">Select Difficulty</option>
                <option value="101">Easy</option>
                <option value="102">Moderate</option>
                <option value="103">Hard</option>
            </select>
            <textarea name="description" placeholder="Question Description" required></textarea>
            <input type="text" name="option1" placeholder="Option 1" required />
            <input type="text" name="option2" placeholder="Option 2" required />
            <input type="text" name="option3" placeholder="Option 3" required />
            <input type="text" name="option4" placeholder="Option 4" required />
            <input type="text" name="correct" placeholder="Correct Answer (must match one of the options)" required />
            <button name="insert">Insert Question</button>
        </form>

        <h2>Current Questions</h2>
        <div class="question-list">
            <?php if ($questions_result->num_rows > 0): ?>
                <?php while($row = $questions_result->fetch_assoc()): ?>
                    <div class="question-item">
                        <p><strong>Question:</strong> <?= $row['Q_description'] ?></p>
                        <p><strong>Options:</strong> <?= $row['Option_1'] ?>, <?= $row['Option_2'] ?>, <?= $row['Option_3'] ?>, <?= $row['Option_4'] ?></p>
                        <p><strong>Correct Answer:</strong> <?= $row['Correct_Ans'] ?></p>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="qid" value="<?= $row['Q_id'] ?>" />
                            <button name="delete">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No questions found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
