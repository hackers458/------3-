<?php
ini_set('session.save_path', '/tmp');
session_start();
require_once 'auth_check.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "잘못된 접근입니다.";
    exit;
}

$post_id = (int)$_GET['id'];

$conn = new mysqli("kknock6.mysql.database.azure.com", "hackers458", "swjisj123!", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 게시글 작성자 확인
$stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($user_id);
if (!$stmt->fetch()) {
    echo "게시글이 존재하지 않습니다.";
    exit;
}
$stmt->close();

// 권한 체크 (타입 일치 중요)
if ((int)$_SESSION['user_id'] !== (int)$user_id) {
    echo "삭제 권한이 없습니다.";
    exit;
}

// 게시글 삭제
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);

if ($stmt->execute()) {
    header("Location: board_list.php");
    exit;
} else {
    echo "삭제 실패: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
