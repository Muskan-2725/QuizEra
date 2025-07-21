<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>QuizEra | Home</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
 <header class="top-bar">
  <img src="assets/image/logo.png" alt="Quizera Logo" class="logo" />
  <button id="themeToggle">🌓</button>
  <?php if ($isLoggedIn): ?>
    <span class="welcome-msg">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
    <a href="php/leaderboard.php" class="admin-link">🏆 Leaderboard</a>
    <a href="admin.php" class="admin-link">🔐 Admin</a>
     <a href="php/logout.php" class="logout-btn">Logout</a><!-- 👈 Add this line here -->
  <?php endif; ?>
</header>


  <div class="site-title-wrapper">
    <h1 class="site-title">Quizera</h1>
    <p class="tagline">Challenge your brain every day!</p>
  </div>

  <?php if (!$isLoggedIn): ?>
  <main class="auth-container">
    <div id="register-form">
      <h2>Register</h2>
      <input type="text" id="registerUsername" placeholder="Username" />
      <input type="password" id="registerPassword" placeholder="Password" />
      <button onclick="registerUser()">Register</button>
      <p>Already registered? <a href="#" onclick="toggleForms()">Login here</a></p>
    </div>

    <div id="login-form" style="display:none;">
      <h2>Login</h2>
      <input type="text" id="loginUsername" placeholder="Username" />
      <input type="password" id="loginPassword" placeholder="Password" />
      <button onclick="loginUser()">Login</button>
      <p>New user? <a href="#" onclick="toggleForms()">Register here</a></p>
    </div>
  </main>
  <?php else: ?>
  <main class="category-container">
    <div class="card">
      <h3>🧠 General Knowledge</h3>
      <button onclick="startQuiz('general')">Start Quiz</button>
    </div>
    <div class="card">
      <h3>➗ Mathematics</h3>
      <button onclick="startQuiz('math')">Start Quiz</button>
    </div>
    <div class="card">
      <h3>💻 Technology</h3>
      <button onclick="startQuiz('tech')">Start Quiz</button>
    </div>
  </main>
  <!-- <div style="text-align: center; margin-top: 2rem;">
  <a href="admin.php" style="font-weight: bold; text-decoration: none; color: #6a1b9a;">🔐 Admin Login</a>
</div> -->
  <?php endif; ?>

  <script>
    function toggleForms() {
      const reg = document.getElementById("register-form");
      const log = document.getElementById("login-form");
      if (reg.style.display === "none") {
        reg.style.display = "block";
        log.style.display = "none";
      } else {
        reg.style.display = "none";
        log.style.display = "block";
      }
    }

    function registerUser() {
      const username = document.getElementById("registerUsername").value;
      const password = document.getElementById("registerPassword").value;

      fetch('php/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Registration successful!");
          location.reload(); // Reload to show quiz categories
        } else {
          alert(data.message);
        }
      });
    }

    function loginUser() {
      const username = document.getElementById("loginUsername").value;
      const password = document.getElementById("loginPassword").value;

      fetch('php/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Login successful!");
          location.reload(); // Reload to show quiz categories
        } else {
          alert(data.message);
        }
      });
    }

    function startQuiz(category) {
      window.location.href = `quiz.html?category=${category}`;
    }
  </script>
  <script src="js/theme.js"></script>
</body>
</html>
