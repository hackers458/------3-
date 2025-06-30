<?php
session_start(); // 세션 시작
 
if(!isset($_SESSION['user_id'])) // 로그인되어있지 않다면
{
    header ('Location: ./index.html'); // 로그인 페이지로 이동
}
 
echo "<h2>로그인 성공</h2><br><h2>";
echo $_SESSION['name'];
echo " 님 안녕하세요</h2><br><br>"; // 로그인한 사용자의 이름 출력
echo "<a href=logout.php>로그아웃</a>"; // 로그아웃 링크 출력
 
?>
