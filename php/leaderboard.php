<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require 'db.php';

// Fetch top 10 scores across all categories (edit as needed)
$sql = "SELECT u.username, s.score, s.total_questions, s.time_taken, s.category, s.submitted_at
        FROM scores s
        JOIN users u ON s.user_id = u.id
        ORDER BY s.score DESC, s.time_taken ASC
        LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard | QuizEra</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Leaderboard custom styles */
        .leaderboard-wrapper {
            max-width: 720px;
            margin: 45px auto 20px;
            background: #fff;
            border-radius: 16px;
            padding: 32px 28px;
            box-shadow: 0 6px 32px rgba(60,36,163,0.07), 0 1.5px 4px rgba(0,0,0,0.08);
        }
        .leaderboard-title {
            font-size: 2.2rem;
            text-align: center;
            font-weight: 600;
            color: #4a148c;
            margin-bottom: 28px;
            letter-spacing: 0.2px;
        }
        .leaderboard-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5em;
            background: none;
        }
        .leaderboard-table th, .leaderboard-table td {
            padding: 12px 16px;
            font-size: 1.08rem;
            border: none;
            text-align: center;
        }
        .leaderboard-table th {
            background-color: #ece6fa;
            color: #563d7c;
            font-weight: 700;
            border-radius: 8px 8px 0 0;
        }
        .leaderboard-table tr {
            background: #fbfaff;
            transition: box-shadow 0.18s;
        }
        .leaderboard-table tr:hover {
            box-shadow: 0 2px 18px #e1bee767;
        }
        .leaderboard-table td {
            border-radius: 5px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 22px;
            padding: 8px 20px;
            background: #dfc5ff;
            border-radius: 8px;
            color: #54238c;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.18s;
        }
        .back-link:hover {
            background: #ce93d8;
            color: #260a3b;
        }
        /* Dark mode override */
        body.dark .leaderboard-wrapper {
            background: #222239;
            box-shadow: 0 6px 32px rgba(44,42,88,0.17), 0 1.5px 4px rgba(0,0,0,0.14);
        }
        body.dark .leaderboard-table th {
            background-color: #533ea6;
            color: #f3e8ff;
        }
        body.dark .leaderboard-table tr {
            background: #332d5a;
        }
        body.dark .back-link {
            background: #39295c;
            color: #e5d8f9;
        }
        body.dark .back-link:hover {
            background: #5f2c82;
            color: #fff;
        }
        @media (max-width: 600px) {
            .leaderboard-wrapper { padding: 16px 2px; }
            .leaderboard-title { font-size: 1.25rem; }
            .leaderboard-table td, .leaderboard-table th { font-size: 0.9rem; padding: 8px 4px; }
        }
    </style>
</head>
<body>
    <div class="leaderboard-wrapper">
        <a href="../index.php" class="back-link">← Home</a>
        <h1 class="leaderboard-title">🏆 Leaderboard</h1>
        <table class="leaderboard-table">
            <tr>
                <th>Rank</th>
                <th>Username</th>
                <th>Score</th>
                <th>Total</th>
                <th>Time Taken (s)</th>
                <th>Category</th>
                <th>Date</th>
            </tr>
            <?php if (count($rows) === 0): ?>
                <tr><td colspan="7">No scores available</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $idx => $row): ?>
                    <tr <?php if ($idx === 0) echo 'style="background:linear-gradient(90deg,#fffbe6,#ffe0b2);"'; ?>>
                        <td><?= $idx + 1 ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= $row['score'] ?></td>
                        <td><?= $row['total_questions'] ?></td>
                        <td><?= $row['time_taken'] ?? '-' ?></td>
                        <td><?= ucfirst($row['category'] ?? '-') ?></td>
                        <td><?= $row['submitted_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
    <script src="../js/theme.js"></script>
</body>
</html>
