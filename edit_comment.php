<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("잘못된 접근입니다.");
}

$comment_id = (int)$_GET['id'];

$conn = new mysqli("localhost", "root", "swjisj123!B", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 댓글과 작성자 정보 가져오기
$stmt = $conn->prepare("SELECT content, user_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->bind_result($content, $comment_user_id);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    die("댓글이 존재하지 않습니다.");
}
$stmt->close();

// 로그인한 사용자만, 그리고 댓글 작성자만 수정 가능
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $comment_user_id) {
    $conn->close();
    die("수정 권한이 없습니다.");
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>댓글 수정</title>
</head>
<body>
    <h3>댓글 수정</h3>
    <form action="update_comment.php" method="post">
        <input type="hidden" name="comment_id" value="<?= $comment_id ?>">
        <textarea name="content" rows="5" cols="60" required><?= htmlspecialchars($content) ?></textarea><br>
        <input type="submit" value="수정 완료">
    </form>
    <p><a href="javascript:history.back()">취소</a></p>
</body>
</html>
