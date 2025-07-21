<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  http_response_code(403);
  echo json_encode(['message' => 'Unauthorized']);
  exit();
}

require 'db.php';

$result = $conn->query("SELECT * FROM questions ORDER BY id DESC");
$questions = [];

while ($row = $result->fetch_assoc()) {
  $questions[] = $row;
}

echo json_encode($questions);
?>
