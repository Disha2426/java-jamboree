let currentQuestion = 0;
let score = 0;
let questions = [];
let timerInterval;

function startQuiz(testId) {
  fetch(`quiz.php?test_id=${testId}`)
    .then(res => res.json())
    .then(data => {
      questions = data;
      document.querySelector('.level-selection').style.display = 'none';
      document.getElementById('quiz-container').style.display = 'flex';
      startTimer();
      showQuestion();
    });
}

function startTimer() {
  let seconds = 0;
  timerInterval = setInterval(() => {
    seconds++;
    document.getElementById('timer').innerText = `00:${seconds < 10 ? '0' + seconds : seconds}`;
  }, 1000);
}

function showQuestion() {
  if (currentQuestion >= questions.length) {
    clearInterval(timerInterval);
    document.getElementById('quiz-container').style.display = 'none';
    document.getElementById('final-scoreboard').style.display = 'block';
    document.getElementById('final-score').innerText = `${score}/${questions.length}`;
    return;
  }

  const q = questions[currentQuestion];
  const quizBox = document.getElementById('quiz-box');

  quizBox.innerHTML = `
    <div class="question-container">
      <h3>${currentQuestion + 1}. ${q.Q_description}</h3>
    </div>
    <div class="options">
      ${[1, 2, 3, 4].map(i => `
        <div class="option-btn" onclick="selectOption(this, '${q['Option_' + i]}', '${q.Correct_Ans}')">
          ${q['Option_' + i]}
        </div>
      `).join('')}
    </div>
    <div class="info-link">
      <a href="#">Click here for more information</a>
    </div>
    <div class="controls">
      <button class="submit-btn" onclick="submitAnswer()">Submit</button>
      <button class="skip-btn" onclick="skipQuestion()">Skip</button>
      <button class="back-btn" onclick="goBack()">Back</button>
    </div>
  `;

  updateStatus();
}

function selectOption(button, selected, correct) {
  const buttons = button.parentElement.querySelectorAll('.option-btn');
  buttons.forEach(btn => btn.onclick = null); // Disable further clicks

  if (selected === correct) {
    button.classList.add('green');
    score++;
  } else {
    button.classList.add('red');
    buttons.forEach(btn => {
      if (btn.innerText === correct) btn.classList.add('green');
    });
  }

  // Save that the question was attempted
  updateStatus(true);
}

function submitAnswer() {
  currentQuestion++;
  showQuestion();
}

function skipQuestion() {
  currentQuestion++;
  showQuestion();
}

function goBack() {
  if (currentQuestion > 0) currentQuestion--;
  showQuestion();
}

function updateStatus(markAttempted = false) {
  const list = document.getElementById('question-status-list');
  list.innerHTML = questions.map((_, index) => {
    let status;
    if (index < currentQuestion) {
      status = 'Attempted';
    } else if (index === currentQuestion && markAttempted) {
      status = 'Attempted';
    } else {
      status = 'Not Attempted';
    }
    return `<li style="color: ${status === 'Attempted' ? 'green' : 'red'}">Question ${index + 1}: ${status}</li>`;
  }).join('');
}
