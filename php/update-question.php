<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$question = $data['question'];
$optionA = $data['optionA'];
$optionB = $data['optionB'];
$optionC = $data['optionC'];
$optionD = $data['optionD'];
$correct = $data['correctOption'];

$stmt = $conn->prepare("UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE id = ?");
$stmt->bind_param("ssssssi", $question, $optionA, $optionB, $optionC, $optionD, $correct, $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to update']);
}
?>
