<?php
session_start();
require_once 'auth_check.php'; // 로그인 체크용 파일, 없으면 직접 로그인 확인 코드 추가

if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
    die("잘못된 요청입니다.");
}

$post_id = (int)$_POST['post_id'];
$user_id = $_SESSION['user_id'];
$content = trim($_POST['content']);

if ($content === '') {
    die("댓글 내용을 입력하세요.");
}

$conn = new mysqli("localhost", "root", "swjisj123!B", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 게시글 존재 여부 확인 (선택 사항)
$stmt = $conn->prepare("SELECT id FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    $conn->close();
    die("게시글이 존재하지 않습니다.");
}
$stmt->close();

// 댓글 저장
$stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $post_id, $user_id, $content);
if (!$stmt->execute()) {
    die("댓글 저장 실패: " . $stmt->error);
}
$stmt->close();
$conn->close();

header("Location: board_view.php?id=" . $post_id);
exit;
?>
