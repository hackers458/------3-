<?php
session_start();

$conn = new mysqli("localhost", "root", "swjisj123!B", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

// 이메일로 사용자 조회
$stmt = $conn->prepare("SELECT id, password, nickname FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        // 로그인 성공 → 세션 저장
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nickname'] = $user['nickname'];
        header("Location: main.php"); // 로그인 후 이동
        exit;
    } else {
        echo "비밀번호가 올바르지 않습니다.";
    }
} else {
    echo "가입되지 않은 이메일입니다.";
}
?>