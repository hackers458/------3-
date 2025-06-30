<?php
$conn = new mysqli("kknock6.mysql.database.azure.com", "hackers458", "swjisj123!", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$nickname = $_POST['nickname'];

// 이메일 중복 검사
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$stmt) {
    die("이메일 중복 검사 준비 실패: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "이미 가입된 이메일입니다.";
    exit;
}
$stmt->close();

// 닉네임 중복 검사 추가
$stmt = $conn->prepare("SELECT id FROM users WHERE nickname = ?");
if (!$stmt) {
    die("닉네임 중복 검사 준비 실패: " . $conn->error);
}
$stmt->bind_param("s", $nickname);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "이미 사용 중인 닉네임입니다.";
    exit;
}
$stmt->close();

// 사용자 정보 삽입
$stmt = $conn->prepare("INSERT INTO users (email, password, nickname) VALUES (?, ?, ?)");
if (!$stmt) {
    die("삽입 쿼리 준비 실패: " . $conn->error);
}
$stmt->bind_param("sss", $email, $password, $nickname);

if ($stmt->execute()) {
    header("Location: index.html");
    exit;
} else {
    echo "회원가입 실패: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
