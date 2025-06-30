<?php
session_start();

if (!isset($_POST['comment_id']) || !is_numeric($_POST['comment_id'])) {
    die("잘못된 접근입니다.");
}

$comment_id = (int)$_POST['comment_id'];
$content = trim($_POST['content']);
if ($content === '') {
    die("댓글 내용을 입력하세요.");
}

$conn = new mysqli("localhost", "root", "swjisj123!B", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 댓글 작성자 확인
$stmt = $conn->prepare("SELECT user_id, post_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->bind_result($comment_user_id, $post_id);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    die("댓글이 존재하지 않습니다.");
}
$stmt->close();

// 로그인한 사용자만, 댓글 작성자만 수정 가능
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $comment_user_id) {
    $conn->close();
    die("수정 권한이 없습니다.");
}

// 댓글 수정
$stmt = $conn->prepare("UPDATE comments SET content = ?, created_at = NOW() WHERE id = ?");
$stmt->bind_param("si", $content, $comment_id);
if (!$stmt->execute()) {
    $stmt->close();
    $conn->close();
    die("댓글 수정 실패: " . $stmt->error);
}
$stmt->close();
$conn->close();

// 수정 후 다시 게시글 상세 페이지로 이동
header("Location: board_view.php?id=" . $post_id);
exit;
?>
