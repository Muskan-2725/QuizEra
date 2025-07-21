<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
header('Content-Type: application/json');

// Get category from URL query param
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!$category) {
  echo json_encode(['error' => 'Category not specified']);
  exit;
}

$stmt = $conn->prepare("SELECT * FROM questions WHERE category = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];

while ($row = $result->fetch_assoc()) {
  $questions[] = [
    'question' => $row['question_text'],
    'options' => [
      $row['option_a'],
      $row['option_b'],
      $row['option_c'],
      $row['option_d']
    ],
    'answer' => $row['correct_option']
  ];
}

echo json_encode($questions);
