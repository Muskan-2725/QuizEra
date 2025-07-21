<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Check admin access
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$category = $data['category'];
$question = $data['question'];
$a = $data['optionA'];
$b = $data['optionB'];
$c = $data['optionC'];
$d = $data['optionD'];
$correct = $data['correctOption'];

$stmt = $conn->prepare("INSERT INTO questions (category, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $category, $question, $a, $b, $c, $d, $correct);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "✅ Question added successfully!"]);
} else {
  echo json_encode(["success" => false, "message" => "❌ Error: " . $conn->error]);
}
?>
