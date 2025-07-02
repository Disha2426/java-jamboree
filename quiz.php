<?php
session_start();
include 'db_conn.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}
$user_email = $_SESSION['user'];

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.html");
    exit;
}

// Fetch quiz history
$attempts = 0;
$total_score = 0;
$history = $conn->query("SELECT COUNT(*) as attempts, SUM(score) as total_score FROM quiz_attempts WHERE user_email = '$user_email'");
if ($history && $row = $history->fetch_assoc()) {
    $attempts = $row['attempts'];
    $total_score = $row['total_score'] ?? 0;
}

$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
$start = isset($_GET['start']);
$questions = [];

if ($test_id && $start) {
    $sql = "SELECT * FROM question_bank WHERE Test_id = $test_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Java Jamboree Quiz</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #74ebd5, #9face6);
      margin: 0; padding: 0;
    }
    .top-bar {
      background: #333;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      position: relative;
    }
    .user-icon {
      cursor: pointer;
      position: relative;
    }
    .dropdown {
      position: absolute;
      top: 50px;
      right: 20px;
      background: #fff;
      color: #333;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      display: none;
      min-width: 200px;
      z-index: 1000;
    }
    .dropdown.active {
      display: block;
    }
    .dropdown p, .dropdown form {
      padding: 10px;
      margin: 0;
      border-bottom: 1px solid #ddd;
    }
    .dropdown p:last-child, .dropdown form:last-child {
      border-bottom: none;
    }
    .dropdown button {
      width: 100%;
      background: none;
      border: none;
      padding: 10px;
      cursor: pointer;
      font-size: 16px;
      color: #333;
    }
    h2 { text-align: center; color: #fff; margin: 30px 0 20px; }
    .difficulty-buttons, .start-btn {
      display: flex; justify-content: center; gap: 20px; margin: 30px auto;
    }
    .difficulty-buttons a button, .start-btn button {
      padding: 12px 25px; font-size: 16px; background: #2575fc;
      color: white; border: none; border-radius: 8px; cursor: pointer;
      transition: background 0.3s ease;
    }
    .difficulty-buttons a button:hover, .start-btn button:hover {
      background: #6a11cb;
    }
    .container {
      max-width: 1000px; margin: 0 auto 40px;
      background: #ffffffee; padding: 30px; border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1); display: flex; gap: 30px;
    }
    .quiz-area { flex: 3; }
    .question { display: none; margin-bottom: 25px; }
    .question.active { display: block; }
    .options label {
      display: block; margin: 8px 0; padding: 10px;
      background: #f9f9f9; border-radius: 6px; cursor: pointer;
    }
    .btn-row { display: flex; justify-content: space-between; margin-top: 20px; }
    .btn {
      padding: 10px 20px; background: #2575fc; color: white;
      border: none; border-radius: 6px; font-size: 16px; cursor: pointer;
    }
    .dashboard {
      flex: 1; background: #f4f6f8; border-radius: 10px; padding: 20px; font-size: 16px; color: #333;
    }
    #timer { text-align: center; font-size: 18px; margin: 15px 0; font-weight: bold; color: #2575fc; }
    .result-screen { text-align: center; padding: 40px; }
    .correct { background: #d4edda !important; border-left: 5px solid #28a745; }
    .incorrect { background: #f8d7da !important; border-left: 5px solid #dc3545; }
    .instructions {
      max-width: 700px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
<div class="top-bar">
  <div class="user-icon" onclick="toggleDropdown()">
    👤 <?= htmlspecialchars($user_email); ?>
    <div class="dropdown" id="dropdownMenu">
      <p><strong>User:</strong> <?= htmlspecialchars($user_email); ?></p>
      <p><strong>Attempts:</strong> <?= $attempts; ?></p>
      <p><strong>Total Score:</strong> <?= $total_score; ?></p>
      <form method="post">
        <button type="submit" name="logout">Logout</button>
      </form>
    </div>
  </div>
</div>

<?php if (!$test_id): ?>
  <br><br><br><br><br><br><br>
  <h2 style="font-size:50px">Select Difficulty Level</h2>
  <div class="difficulty-buttons">
    <a href="?test_id=101"style="padding: 16px 32px;       /* Bigger button area */
  font-size: 18px;          /* Larger text */
  border-radius: 8px;       /* Rounded edges */
  border: none;
  background-color: #2575fc;
  color: white;
  cursor: pointer;
  transition: 0.3s ease;"><button>Easy</button></a>
    <a href="?test_id=102"style="padding: 16px 32px;       /* Bigger button area */
  font-size: 18px;          /* Larger text */
  border-radius: 8px;       /* Rounded edges */
  border: none;
  background-color: #2575fc;
  color: white;
  cursor: pointer;
  transition: 0.3s ease;"><button>Moderate</button></a>
    <a href="?test_id=103"style="padding: 16px 32px;       /* Bigger button area */
  font-size: 18px;          /* Larger text */
  border-radius: 8px;       /* Rounded edges */
  border: none;
  background-color: #2575fc;
  color: white;
  cursor: pointer;
  transition: 0.3s ease;"><button>Hard</button></a>
  </div>

<?php elseif ($test_id && !$start): ?>
  <div class="instructions">
    <h3>Instructions:</h3>
    <ul>
      <li>This quiz contains multiple-choice questions.</li>
      <li>You can use the navigation buttons to move between questions.</li>
      <li>You can skip a question and return later.</li>
      <li>Timer will start once you begin the quiz.</li>
      <li>Click "Submit Quiz" to finish and record your score.</li>
    </ul>
    <div class="start-btn"><a href="?test_id=<?= $test_id ?>&start=true"><button>Start Quiz</button></a></div>
  </div>

<?php elseif ($test_id && count($questions) > 0): ?>
  <h2>Java Jamboree Quiz</h2>
  <div class="container">
    <div class="quiz-area" id="quizArea">
      <div id="timer">Time Elapsed: 00:00</div>
      <form id="quizForm">
        <?php foreach ($questions as $index => $q): ?>
          <div class="question" data-index="<?= $index; ?>" data-correct="<?= htmlspecialchars($q['Correct_Ans']); ?>">
            <p>Q<?= $index + 1; ?>: <?= htmlspecialchars($q['Q_description']); ?></p>
            <div class="options">
              <?php for ($i = 1; $i <= 4; $i++): ?>
                <label>
                  <input type="radio" name="q<?= $q['Q_id']; ?>" value="<?= htmlspecialchars($q["Option_$i"]); ?>">
                  <?= htmlspecialchars($q["Option_$i"]); ?>
                </label>
              <?php endfor; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="btn-row">
          <button type="button" class="btn" onclick="prevQuestion()" style="background-color:red">Back</button>
          <button type="button" class="btn" onclick="skipQuestion()" style="background-color:orange">Skip</button>
          <button type="button" class="btn" onclick="nextQuestion()" style="background-color:green">Next</button>
        </div><br>
        <button type="button" class="btn" style="width:100%;" onclick="submitQuiz()">Submit Quiz</button>
      </form>
    </div>
    <div class="dashboard" id="dashboard">
      <h3>Progress</h3>
      <p>Total Questions: <span id="totalQ"></span></p>
      <p>Attempted: <span id="attemptedQ">0</span></p>
      <p>Skipped: <span id="skippedQ">0</span></p>
      <p>Remaining: <span id="remainingQ"></span></p>
    </div>
  </div>
<?php else: ?>
  <p style="text-align:center; color:#fff;">No questions found for this difficulty.</p>
<?php endif; ?>

<script>
let current = 0;
let total = document.querySelectorAll('.question').length;
let skipped = new Set();
let secondsElapsed = 0;
let timerInterval;

if (total > 0) {
  timerInterval = setInterval(() => {
    secondsElapsed++;
    const minutes = Math.floor(secondsElapsed / 60);
    const seconds = secondsElapsed % 60;
    document.getElementById('timer').textContent = `Time Elapsed: ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
  }, 1000);
}

function updateView() {
  document.querySelectorAll('.question').forEach((q, i) => {
    q.classList.toggle('active', i === current);
  });
  updateDashboard();
}
function nextQuestion() {
  const question = document.querySelectorAll('.question')[current];
  const correct = question.dataset.correct.trim();
  const inputs = question.querySelectorAll('input[type=radio]');
  let selected = false;
  inputs.forEach(input => {
    const label = input.closest('label');
    label.classList.remove('correct', 'incorrect');
    if (input.checked) {
      selected = true;
      label.classList.add(input.value.trim() === correct ? 'correct' : 'incorrect');
    }
  });
  if (!selected) nextStep();
  else setTimeout(nextStep, 800);
}
function nextStep() {
  if (current < total - 1) current++;
  updateView();
}
function prevQuestion() {
  if (current > 0) current--;
  updateView();
}
function skipQuestion() {
  skipped.add(current);
  nextStep();
}
function updateDashboard() {
  document.getElementById('totalQ').textContent = total;
  let attempted = 0;
  document.querySelectorAll('.question').forEach(q => {
    q.querySelectorAll('input[type=radio]').forEach(input => {
      if (input.checked) attempted++;
    });
  });
  document.getElementById('attemptedQ').textContent = attempted;
  document.getElementById('skippedQ').textContent = skipped.size;
  document.getElementById('remainingQ').textContent = total - attempted - skipped.size;
}
function submitQuiz() {
  clearInterval(timerInterval);
  let score = 0;
  document.querySelectorAll('.question').forEach(q => {
    const correct = q.dataset.correct.trim();
    const inputs = q.querySelectorAll('input[type=radio]');
    inputs.forEach(input => {
      const label = input.closest('label');
      if (input.checked) {
        if (input.value.trim() === correct) {
          label.classList.add('correct');
          score++;
        } else {
          label.classList.add('incorrect');
        }
      }
    });
  });
  const timerText = document.getElementById('timer').textContent.replace('Time Elapsed: ', '');
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "quiz.php?record=1");
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send(`score=${score}&total=${total}&time=${timerText}`);
  document.querySelector('.container').innerHTML = `
    <div class="result-screen" style="margin-top:-80px;">
      <h2>Quiz Completed!</h2>
      <p style="font-size:30px;margin-left:350px"><strong>Score:</strong> ${score} out of ${total}</p>
      <p style="font-size:30px;margin-left:350px"><strong>Time Taken:</strong> ${timerText}</p>
      <a href="quiz.php"><button class="btn" style="margin-top: 20px;margin-left:350px">Try Another Quiz</button></a>
    </div>`;
}

if (total > 0) updateView();

// Toggle dropdown
function toggleDropdown() {
  const dropdown = document.getElementById('dropdownMenu');
  dropdown.classList.toggle('active');
}
document.addEventListener('click', function(event) {
  const userIcon = document.querySelector('.user-icon');
  const dropdown = document.getElementById('dropdownMenu');
  if (!userIcon.contains(event.target)) {
    dropdown.classList.remove('active');
  }
});
</script>

<?php
if (isset($_GET['record']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = intval($_POST['score']);
    $total = intval($_POST['total']);
    $time = $conn->real_escape_string($_POST['time']);
    $conn->query("INSERT INTO quiz_attempts (user_email, test_id, score, total_questions, time_taken)
                  VALUES ('$user_email', $test_id, $score, $total, '$time')");
    exit;
}
?>
</body>
</html>