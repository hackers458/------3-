<?php
session_start();
require_once 'auth_check.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "잘못된 접근입니다.";
    exit;
}

$post_id = (int)$_GET['id'];

$conn = new mysqli("localhost", "root", "swjisj123!B", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 게시글 작성자 확인 및 기존 내용 불러오기
$stmt = $conn->prepare("SELECT title, content, user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($title, $content, $user_id);
if (!$stmt->fetch()) {
    echo "게시글이 존재하지 않습니다.";
    exit;
}
$stmt->close();

if ($_SESSION['user_id'] !== $user_id) {
    echo "수정 권한이 없습니다.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_title, $new_content, $post_id);
    if ($stmt->execute()) {
        header("Location: board_view.php?id=$post_id");
        exit;
    } else {
        echo "수정 실패: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>게시글 수정</title>
</head>
<body>
    <h2>게시글 수정</h2>
    <form method="post" action="">
        <label>제목: <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required></label><br><br>
        <label>내용:<br>
            <textarea name="content" rows="10" cols="50" required><?= htmlspecialchars($content) ?></textarea>
        </label><br><br>
        <input type="submit" value="수정 완료">
    </form>
    <p><a href="board_view.php?id=<?= $post_id ?>">취소</a></p>
</body>
</html>