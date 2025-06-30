<?php
$id = $_POST['user_id']; // 사용자가 입력한 ID
$pw = $_POST['user_pwd']; // 사용자가 입력한 비밀번호
$name = $_POST['name']; // 사용자가 입력한 비밀번호
 
if($id==NULL || $pw==NULL) // 입력한 값이 빈칸인 경우
{
    echo "빈 칸을 모두 채워주세요";
    echo "<a href=sign_up.html>back page</a>";
    exit();
}

$servername = "localhost"; // 서버 이름
$username = "root"; // 사용자 이름
$password = "swjisj123!B"; // 비밀번호
$dbname = "test1"; // 데이터베이스 이름

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 데이터베이스 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 입력한 ID가 이미 존재하는 경우
$sql = "SELECT * FROM users WHERE user_id='$id'";
$result = $conn->query($sql);

if($result->num_rows == 1)
{
    echo "중복된 이메일일입니다.";
    echo "<a href=sign_up.html>back page</a>";
    exit();
}

// 새로운 사용자 정보를 데이터베이스에 추가
$sql = "INSERT INTO users (user_id, user_pwd, name) VALUES ('$id', '$pw', '$name')";
$signup = mysqli_query($conn, $sql);

// 회원가입이 성공적으로 처리된 경우
if($signup)
{
    echo "회원가입이 완료되었습니다.";
}
 
$conn->close(); // 데이터베이스 연결 종료
?>
