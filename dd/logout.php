<?php
session_start(); // 세션 시작

$res = session_destroy(); // 모든 세션 변수 지우기

if($res)
{
    header('Location: ./Main.php'); // 로그아웃 성공 시 로그인 페이지로 이동
}
?>
