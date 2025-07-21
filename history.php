<?php
session_start();
require 'php/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT score, total_questions, time_taken, submitted_at, category FROM scores WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Quiz History | QuizEra</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body { font-family: sans-serif; padding: 2rem; background: #f5f5f5; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { border: 1px solid #ccc; padding: 0.7rem; text-align: center; }
    th { background-color: #eee; }
    h2 { color: #333; }
  </style>
</head>
<body>
  <h2>📊 <?= htmlspecialchars($username) ?>'s Quiz History</h2>

  <table>
    <thead>
      <tr>
        <th>Score</th>
        <th>Total</th>
        <th>Time Taken (s)</th>
        <th>Category</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($rows) === 0): ?>
        <tr><td colspan="5">No quiz history found</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><?= $r['score'] ?></td>
          <td><?= $r['total_questions'] ?></td>
          <td><?= $r['time_taken'] ?></td>
          <td><?= ucfirst($r['category']) ?></td>
          <td><?= $r['submitted_at'] ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</body>
</html>
