// ==== Display Final Score ====
const score = sessionStorage.getItem("score");
const total = sessionStorage.getItem("total");
document.getElementById("score-text").innerHTML = `<h2>Score: ${score}/${total}</h2>`;

// ==== Load Review Data ====
const review = JSON.parse(localStorage.getItem("quizReview") || "[]");
const tbody = document.getElementById("review-table-body");

review.forEach((q, index) => {
  const tr = document.createElement("tr");

  const yourAnswerText = q.selected ? `${q.selected}: ${q.options[q.selected]}` : "Not answered";
  const correctAnswerText = `${q.correct}: ${q.options[q.correct]}`;

  const isCorrect = q.selected === q.correct;

  tr.innerHTML = `
    <td>${index + 1}</td>
    <td>${q.question}</td>
    <td class="${isCorrect ? 'correct' : 'wrong'}">${yourAnswerText}</td>
    <td class="correct">${correctAnswerText}</td>
  `;

  tbody.appendChild(tr);
});
