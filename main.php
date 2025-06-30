<?php
session_start(); // 세션 시작

if (!isset($_SESSION['user_id'])) {
    header('Location: ./index.html');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>메인 페이지</title>
</head>
<body>
    <h2>로그인 성공</h2>
    <h3><?= htmlspecialchars($_SESSION['nickname']) ?> 님 안녕하세요</h3>
    <p>
        <a href="board_list.php">게시판 가기</a><br><br>
        <a href="logout.php">로그아웃</a>
    </p>
</body>
</html>