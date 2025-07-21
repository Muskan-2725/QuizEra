<?php
session_start();
header("Content-Type: application/json"); // Always return JSON

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$score = $data['score'] ?? 0;
$total = $data['total'] ?? 0;
$time = $data['time_taken'] ?? 0;
$category = $data['category'] ?? '';
$user_id = $_SESSION['user_id'];

// Always specify the column names!
$stmt = $conn->prepare("INSERT INTO scores (user_id, score, total_questions, time_taken, category) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiss", $user_id, $score, $total, $time, $category);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Score submitted."]);
} else {
    echo json_encode(["success" => false, "message" => "DB error: " . $conn->error]);
}
?>
