<?php
session_start();
// Uncomment to restrict access to admins only
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//   header("Location: index.php");
//   exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel | QuizEra</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      padding: 2rem;
      background: #f8f9fa;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    h1, h2 {
      color: #343a40;
    }
    .form-container {
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      margin-bottom: 2rem;
    }
    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    .form-row input, .form-row select {
      flex: 1 1 200px;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      padding: 0.6rem 1rem;
      background: #6f42c1;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #5936a2;
    }
    .logout {
      text-decoration: none;
      color: #6f42c1;
      font-weight: bold;
      margin:0 20px;
    }
    .status-msg {
      margin-top: 1rem;
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
    th, td {
      padding: 0.75rem;
      border: 1px solid #dee2e6;
      text-align: left;
    }
    th {
      background: #e9ecef;
    }
    td[contenteditable="true"] {
      background-color: #fff3cd;
    }
    .action-btns button {
      margin-right: 0.3rem;
    }
    .category-section {
      margin-bottom: 2rem;
    }
    .topbar{
        display:flex;
        justify-content:center;
    }
  </style>
</head>
<body>
  <header>
    <h1>🛠️ Admin Panel - Manage Questions</h1>
    <div class="topbar">
    <a class="logout" href="php/logout.php">Logout</a>
    <a class="logout" href="index.php">HOME</a></div>
  </header>

  <div class="form-container">
    <h2>➕ Add New Question</h2>
    <form id="question-form" onsubmit="event.preventDefault(); addQuestion();">
      <div class="form-row">
        <select id="category">
          <option value="general">General Knowledge</option>
          <option value="math">Mathematics</option>
          <option value="tech">Technology</option>
        </select>
        <input type="text" id="question" placeholder="Question" />
        <input type="text" id="optionA" placeholder="Option A" />
        <input type="text" id="optionB" placeholder="Option B" />
        <input type="text" id="optionC" placeholder="Option C" />
        <input type="text" id="optionD" placeholder="Option D" />
        <select id="correctOption">
          <option value="A">Correct: A</option>
          <option value="B">Correct: B</option>
          <option value="C">Correct: C</option>
          <option value="D">Correct: D</option>
        </select>
        <input type="hidden" id="questionId" />
      </div>
      <button type="submit">Submit Question</button>
    </form>
    <div id="status" class="status-msg"></div>
  </div>

  <div class="form-container">
    <h2>📋 Existing Questions</h2>
    <div id="questionSections"></div>
  </div>

  <script>
    async function addQuestion() {
      const category = document.getElementById('category').value.trim();
      const question = document.getElementById('question').value.trim();
      const optionA = document.getElementById('optionA').value.trim();
      const optionB = document.getElementById('optionB').value.trim();
      const optionC = document.getElementById('optionC').value.trim();
      const optionD = document.getElementById('optionD').value.trim();
      const correctOption = document.getElementById('correctOption').value.trim().toUpperCase();
      const statusBox = document.getElementById('status');
      statusBox.textContent = '';

      if (!category || !question || !optionA || !optionB || !optionC || !optionD || !['A','B','C','D'].includes(correctOption)) {
        alert("Please fill all fields and make sure Correct Option is A, B, C, or D.");
        return;
      }

      const data = { category, question, optionA, optionB, optionC, optionD, correctOption };

      try {
        const res = await fetch('php/add-question.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });

        const result = await res.json();
        statusBox.textContent = result.message;
        statusBox.style.color = result.success ? 'green' : 'red';
        if (result.success) {
          document.getElementById('question-form').reset();
          loadQuestions();
        }
      } catch (err) {
        console.error(err);
        statusBox.textContent = "⚠️ Something went wrong. Try again.";
        statusBox.style.color = 'red';
      }
    }

    async function loadQuestions() {
      const res = await fetch('php/get-questions.php');
      const questions = await res.json();
      const sectionDiv = document.getElementById('questionSections');
      sectionDiv.innerHTML = '';

      const categories = ['general', 'math', 'tech'];
      const categoryNames = {
        general: '🧠 General Knowledge',
        math: '🧮 Mathematics',
        tech: '💻 Technology'
      };

      categories.forEach(cat => {
        const filtered = questions.filter(q => q.category === cat);
        const section = document.createElement('div');
        section.className = 'category-section';
        section.innerHTML = `
          <h3>${categoryNames[cat]}</h3>
          <table>
            <thead>
              <tr>
                <th>#</th><th>Question</th><th>A</th><th>B</th><th>C</th><th>D</th><th>Correct</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              ${filtered.map((q, i) => `
                <tr>
                  <td>${i + 1}</td>
                  <td contenteditable="true" id="q-${q.id}">${q.question_text}</td>
                  <td contenteditable="true" id="a-${q.id}">${q.option_a}</td>
                  <td contenteditable="true" id="b-${q.id}">${q.option_b}</td>
                  <td contenteditable="true" id="c-${q.id}">${q.option_c}</td>
                  <td contenteditable="true" id="d-${q.id}">${q.option_d}</td>
                  <td contenteditable="true" id="correct-${q.id}">${q.correct_option}</td>
                  <td class="action-btns">
                    <button onclick="updateQuestion(${q.id})">✏️</button>
                    <button onclick="deleteQuestion(${q.id})">🗑️</button>
                  </td>
                </tr>`).join('')}
            </tbody>
          </table>
        `;
        sectionDiv.appendChild(section);
      });
    }

    async function updateQuestion(id) {
      const question = document.getElementById(`q-${id}`).innerText.trim();
      const optionA = document.getElementById(`a-${id}`).innerText.trim();
      const optionB = document.getElementById(`b-${id}`).innerText.trim();
      const optionC = document.getElementById(`c-${id}`).innerText.trim();
      const optionD = document.getElementById(`d-${id}`).innerText.trim();
      const correctOption = document.getElementById(`correct-${id}`).innerText.trim().toUpperCase();

      const res = await fetch('php/update-question.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, question, optionA, optionB, optionC, optionD, correctOption })
      });

      const result = await res.json();
      alert(result.success ? "✅ Updated successfully!" : "❌ Failed to update");
    }

    async function deleteQuestion(id) {
      if (!confirm("Are you sure you want to delete this question?")) return;

      const res = await fetch('php/delete-question.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });

      const result = await res.json();
      if (result.success) {
        alert("✅ Deleted!");
        loadQuestions();
      } else {
        alert("❌ Failed to delete");
      }
    }

    loadQuestions();
  </script>
</body>
</html>
