let questions = {
    easy: [
        { 
            question: "1. What is 2 + 2?", 
            options: ["3", "4", "5", "6"], 
            correct: 1,
            resource: "https://www.math.com" 
        },
        { 
            question: "2. What is the color of the sky?", 
            options: ["Blue", "Green", "Red", "Yellow"], 
            correct: 0,
            resource: "https://www.space.com" 
        },
        { 
            question: "3. How many legs does a spider have?", 
            options: ["6", "8", "10", "4"], 
            correct: 1,
            resource: "https://www.britannica.com/animal/spider" 
        },
        { 
            question: "4. Which planet is closest to the Sun?", 
            options: ["Earth", "Mercury", "Venus", "Mars"], 
            correct: 1,
            resource: "https://www.nasa.gov" 
        },
        { 
            question: "5. What is the capital of France?", 
            options: ["Rome", "Paris", "Berlin", "Madrid"], 
            correct: 1,
            resource: "https://en.wikipedia.org/wiki/Paris" 
        }
    ],
    moderate: [
        { 
            question: "1. Which of these allows multiple inheritance in Java?", 
            options: ["Interfaces", "Abstract classes", "Enums", "classes"], 
            correct: 0,
            resource: "https://www.geeksforgeeks.org/java-multiple-inheritance/" 
        },
        { 
            question: "2. What is true about classes in Java?", 
            options: ["True", "Not true", "False", "None"], 
            correct: 0,
            resource: "https://www.oracle.com/java/technologies/javase/classes.html" 
        },
        { 
            question: "3. Which of the following is not a valid Java identifier?", 
            options: ["myVariable", "_myVar", "123abc", "$value"], 
            correct: 2,
            resource: "https://www.javatpoint.com/java-identifier" 
        },
        { 
            question: "4. What is the result of true && false || true?", 
            options: ["true", "false", "null", "Error"], 
            correct: 0,
            resource: "https://www.geeksforgeeks.org/java-boolean-operators/" 
        },
        { 
            question: "5. Which collection class allows duplicate elements?", 
            options: ["HashSet", "TreeSet", "LinkedList", "EnumSet"], 
            correct: 2,
            resource: "https://www.geeksforgeeks.org/linkedlist-in-java/" 
        }
    ],
    difficult: [
        { 
            question: "1. What is the output of System.out.println(10 + '10');?", 
            options: ["20", "1010", "Error", "null"], 
            correct: 1,
            resource: "https://www.geeksforgeeks.org/string-concatenation-in-java/" 
        },
        { 
            question: "2. Who painted the Mona Lisa?", 
            options: ["Da Vinci", "Van Gogh", "Picasso", "Dali"], 
            correct: 0,
            resource: "https://en.wikipedia.org/wiki/Mona_Lisa" 
        },
        { 
            question: "3. What is the chemical symbol for gold?", 
            options: ["Au", "Ag", "Fe", "Pb"], 
            correct: 0,
            resource: "https://www.periodic-table.org/gold/" 
        },
        { 
            question: "4. What is the longest river in the world?", 
            options: ["Amazon", "Nile", "Yangtze", "Ganges"], 
            correct: 1,
            resource: "https://www.britannica.com/place/Nile-River" 
        },
        { 
            question: "5. What is the capital of Australia?", 
            options: ["Sydney", "Melbourne", "Canberra", "Brisbane"], 
            correct: 2,
            resource: "https://en.wikipedia.org/wiki/Canberra" 
        }
    ]
};

let currentLevel = '';
let currentQuestionIndex = 0;
let score = 0;
let timerInterval;
let timeLeft = 60; // Timer set to 60 seconds
let answers = []; // To store selected answers (or null if not selected)
let skippedQuestions = []; // To track skipped questions

// Start the quiz based on selected level
function startQuiz(level) {
    currentLevel = level;
    currentQuestionIndex = 0;
    score = 0;
    timeLeft = 60;
    answers = new Array(5).fill(null); // Initialize answers as null (not attempted)
    skippedQuestions = new Array(5).fill(false); // Initialize skipped questions as false
    
    document.querySelector(".level-selection").style.display = 'none';
    document.getElementById('quiz-container').style.display = 'flex';
    document.getElementById('final-scoreboard').style.display = 'none';
    
    startTimer();
    displayQuestion();
    updateDashboard();
}

// Start the countdown timer
function startTimer() {
    timerInterval = setInterval(function() {
        timeLeft--;
        document.getElementById('timer').innerText = `00:${timeLeft < 10 ? '0' + timeLeft : timeLeft}`;
        if (timeLeft === 0) {
            clearInterval(timerInterval);
            showScoreBoard();
        }
    }, 1000);
}

// Display the current question and options
function displayQuestion() {
    const question = questions[currentLevel][currentQuestionIndex];
    document.getElementById('question-text').innerText = question.question;
    
    const optionsContainer = document.querySelector('.options');
    optionsContainer.innerHTML = '';
    question.options.forEach((option, index) => {
        const optionButton = document.createElement('button');
        optionButton.classList.add('option');
        optionButton.textContent = option;
        optionButton.onclick = () => selectOption(index);
        optionsContainer.appendChild(optionButton);
    });
    
    // Display the resource link below the question
    const resourcesContainer = document.querySelector('.resources');
    resourcesContainer.innerHTML = ''; // Clear previous resource if any
    if (question.resource) {
        const resourceLink = document.createElement('a');
        resourceLink.href = question.resource;
        resourceLink.target = "_blank"; // Open link in new tab
        resourceLink.textContent = "Click here for more information";
        resourcesContainer.appendChild(resourceLink);
    }
    
    // Update the question status in the dashboard
    updateDashboard();
}

// Handle the option selection
function selectOption(selectedIndex) {
    const options = document.querySelectorAll('.option');
    
    // Mark the selected option
    options.forEach((option, index) => {
        option.classList.remove('selected'); // Clear previous selection
    });
    options[selectedIndex].classList.add('selected');
    
    // Store the answer
    answers[currentQuestionIndex] = selectedIndex;
}

// Submit the answer and show the result
function submitAnswer() {
    const question = questions[currentLevel][currentQuestionIndex];
    const selectedOption = answers[currentQuestionIndex];
    const optionButtons = document.querySelectorAll('.option');
    
    if (selectedOption === null) {
        alert("Please select an option before submitting.");
        return;
    }
    
    // Mark the correct/incorrect answer
    if (selectedOption === question.correct) {
        optionButtons[selectedOption].classList.add('green');
        score++;
    } else {
        optionButtons[selectedOption].classList.add('red');
        optionButtons[question.correct].classList.add('green');
    }
    
    // Update question status
    const statusList = document.getElementById('question-status');
    const statusItem = document.createElement('li');
    statusItem.textContent = question.question;
    statusItem.classList.add(selectedOption === question.correct ? 'correct' : 'wrong');
    statusList.appendChild(statusItem);
    
    // Disable all options after selection
    optionButtons.forEach(button => button.disabled = true);
    
    // Move to the next question after a brief delay
    setTimeout(() => {
        currentQuestionIndex++;
        if (currentQuestionIndex < 5) {
            displayQuestion();
        } else {
            clearInterval(timerInterval);
            showScoreBoard();
        }
    }, 1000);
}

// Skip the current question and move to the next question
function skipQuestion() {
    skippedQuestions[currentQuestionIndex] = true; // Mark this question as skipped
    currentQuestionIndex++; // Move to the next question
    if (currentQuestionIndex < 5) {
        displayQuestion();
    } else {
        clearInterval(timerInterval);
        showScoreBoard();
    }
}

// Back to the previous question
function goBack() {
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        displayQuestion();
    }
}

// Show the scoreboard after quiz completion
function showScoreBoard() {
    document.getElementById('quiz-container').style.display = 'none';
    document.getElementById('final-scoreboard').style.display = 'block';
    document.getElementById('final-score').innerText = score;
    let feedback = 'Better luck next time!';
    if (score >= 4) {
        feedback = 'Great job!';
    } else if (score === 5) {
        feedback = 'You are a quiz master!';
    }
    document.getElementById('feedback').innerText = feedback;
}

// Update the dashboard to show attempted, not attempted, and skipped questions
function updateDashboard() {
    const questionStatusList = document.getElementById('question-status');
    questionStatusList.innerHTML = ''; // Clear previous status
    
    questions[currentLevel].forEach((question, index) => {
        const statusItem = document.createElement('li');
        statusItem.textContent = `Question ${index + 1}: `;
        
        if (answers[index] !== null) {
            statusItem.textContent += 'Attempted';
            statusItem.classList.add('attempted');
        } else if (skippedQuestions[index]) {
            statusItem.textContent += 'Skipped';
            statusItem.classList.add('skipped');
        } else {
            statusItem.textContent += 'Not Attempted';
            statusItem.classList.add('not-attempted');
        }
        
        questionStatusList.appendChild(statusItem);
    });
}

function resetQuiz() {
    document.querySelector(".level-selection").style.display = 'block';
    document.getElementById('final-scoreboard').style.display = 'none';
}
