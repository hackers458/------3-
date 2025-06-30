<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("잘못된 접근입니다.");
}

$comment_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$conn = new mysqli("kknock6.mysql.database.azure.com", "hackers458", "swjisj123!", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 댓글이 본인의 것인지 확인하고 삭제
$stmt = $conn->prepare("SELECT post_id FROM comments WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $comment_id, $user_id);
$stmt->execute();
$stmt->bind_result($post_id);
if (!$stmt->fetch()) {
    echo "삭제 권한이 없습니다.";
    exit;
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();

header("Location: board_view.php?id=$post_id");
exit;
