let questions = [];
let currentQuestionIndex = 0;
let score = 0;

async function fetchQuestions(testId) {
    try {
        const response = await fetch(`get_questions.php?test_id=${testId}`);
        const data = await response.json();
        questions = data;
        currentQuestionIndex = 0;
        score = 0;
        document.getElementById("quiz-container").style.display = "flex";
        document.querySelector(".level-selection").style.display = "none";
        displayQuestion();
    } catch (err) {
        console.error("Failed to load questions:", err);
    }
}

function displayQuestion() {
    const q = questions[currentQuestionIndex];
    document.getElementById("question-text").innerText = q.question;

    const optionsContainer = document.getElementById("options-container");
    optionsContainer.innerHTML = "";

    q.options.forEach((opt, i) => {
        const btn = document.createElement("button");
        btn.innerText = opt;
        btn.onclick = () => checkAnswer(i);
        optionsContainer.appendChild(btn);
    });
}

function checkAnswer(selected) {
    if (selected === questions[currentQuestionIndex].correct) score++;
    currentQuestionIndex++;

    if (currentQuestionIndex < questions.length) {
        displayQuestion();
    } else {
        document.getElementById("quiz-container").style.display = "none";
        document.getElementById("final-scoreboard").style.display = "block";
        document.getElementById("final-score").innerText = score;
    }
}
