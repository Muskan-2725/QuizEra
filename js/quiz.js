// ===== Question Data =====
// Temporary: Hardcoded questions (can be replaced with API fetch)
let questions = [
 
];
let userAnswers = [];

async function fetchQuestions() {
  const urlParams = new URLSearchParams(window.location.search);
  const category = urlParams.get("category");

  try {
    const res = await fetch(`php/fetch-questions.php?category=${category}`);
    const data = await res.json();

    if (Array.isArray(data)) {
      questions = data;
      selectedAnswers = new Array(questions.length).fill(null);
      showQuestion();
      startTimer();
    } else {
      alert("Failed to load questions.");
    }
  } catch (err) {
    console.error("Error fetching questions:", err);
    alert("Error loading quiz questions.");
  }
}



// ===== State Management =====
let currentQuestion = 0;
let score = 0;
let selectedAnswers = new Array(questions.length).fill(null);

// ===== Timer Setup =====
let timeLeft = 60;
let timer;

function startTimer() {
  const timerDisplay = document.getElementById('timer');
  timerDisplay.textContent = `Time Left: ${timeLeft}s`;

  timer = setInterval(() => {
    timeLeft--;
    timerDisplay.textContent = `Time Left: ${timeLeft}s`;

    if (timeLeft <= 0) {
      clearInterval(timer);
      autoSubmit();
    }
  }, 1000);
}

// ===== On Load =====
window.onload = () => {
  fetchQuestions();
};


// ===== Render Question and Options =====
function showQuestion() {
  const q = questions[currentQuestion];
  const questionBox = document.getElementById("question-box");
  const optionsBox = document.getElementById("options-box");

  questionBox.innerHTML = `<p>${currentQuestion + 1}. ${q.question}</p>`;
  optionsBox.innerHTML = "";

  q.options.forEach((option) => {
    const button = document.createElement("button");
    button.textContent = option;
    button.className = "option-btn";

    // Highlight if selected
    if (selectedAnswers[currentQuestion] === option) {
      button.classList.add("selected");
    }

    button.addEventListener("click", () => {
      selectedAnswers[currentQuestion] = option;
      showQuestion(); // Re-render to show selected
    });

    optionsBox.appendChild(button);
  });
}

// ===== Navigation =====
document.getElementById("nextBtn").addEventListener("click", () => {
  if (currentQuestion < questions.length - 1) {
    currentQuestion++;
    showQuestion();
  }
});

document.getElementById("prevBtn").addEventListener("click", () => {
  if (currentQuestion > 0) {
    currentQuestion--;
    showQuestion();
  }
});

// ===== Manual Submit =====
document.getElementById("submitBtn").addEventListener("click", submitQuiz);

// ===== Submit Logic =====
function submitQuiz() {
  clearInterval(timer);

  selectedAnswers.forEach((ans, index) => {
    const correctIndex = questions[index].answer; // e.g., "A"
    const correctOption = questions[index].options[correctIndex.charCodeAt(0) - 65];
    if (ans === correctOption) score++;
  });

  const urlParams = new URLSearchParams(window.location.search);
  const category = urlParams.get("category");

  // Save in sessionStorage
  sessionStorage.setItem("score", score);
  sessionStorage.setItem("total", questions.length);
  sessionStorage.setItem("category", category);

  // Submit to backend
  fetch('php/submit-score.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  credentials: 'include', // ✅ Very important
  body: JSON.stringify({
    score: score,
    total: questions.length,
    time_taken: 60 - timeLeft,
    category: category
  })
})
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      console.log("✅ Score submitted:", res.message);
      window.location.href = "result.html";
    } else {
      alert("❌ Failed to submit score to server.\n" + res.message);
    }
  })
  .catch(err => {
    console.error("Submit Error:", err);
    alert("❌ Failed to submit score to server.\n" + err.message);
  });



}





// ===== Auto Submit When Timer Ends =====
function autoSubmit() {
  alert("⏰ Time's up! Submitting your quiz...");
  submitQuiz();
}


