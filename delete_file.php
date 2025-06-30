<?php
session_start();
require_once 'auth_check.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("잘못된 접근입니다.");
}

$file_id = (int)$_GET['id'];

$conn = new mysqli("kknock6.mysql.database.azure.com", "hackers458", "swjisj123!", "user_info");

// 파일 정보 + 글 작성자 조회
$stmt = $conn->prepare("
    SELECT f.filename, f.post_id, p.user_id 
    FROM files f
    JOIN posts p ON f.post_id = p.id
    WHERE f.id = ?
");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$stmt->bind_result($filename, $post_id, $owner_id);

if (!$stmt->fetch()) {
    die("파일을 찾을 수 없습니다.");
}
$stmt->close();

// 권한 확인
if ($_SESSION['user_id'] !== $owner_id) {
    die("권한이 없습니다.");
}

// 실제 파일 삭제
$filepath = "uploads/$filename";
if (file_exists($filepath)) {
    unlink($filepath);
}

// DB 삭제
$stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();

header("Location: board_view.php?id=$post_id");
exit;
?>
