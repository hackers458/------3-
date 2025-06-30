<?php
session_start(); // 세션 시작

$id = $_POST['user_id']; // 사용자가 제출한 ID
$pw = $_POST['user_pwd']; // 사용자가 제출한 비밀번호
$name = $_POST['name']; // 사용자가 제출한 비밀번호

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

// 입력한 ID를 가진 사용자 정보를 가져옴
$sql = "SELECT * FROM users WHERE user_id='$id'";
$result = $conn->query($sql);

// 사용자 정보가 존재하는 경우
if($result->num_rows == 1){
    $row = $result->fetch_array(MYSQLI_ASSOC); 
    // 입력한 비밀번호가 맞는 경우
    if($row['user_pwd'] == $pw){  
        $_SESSION['user_id'] = $id;      
        $_SESSION['name'] = $name;     
        // 세션 저장 성공한 경우
        if(isset($_SESSION['user_id'])){    
            header('Location: ./Main.php');   
        }
        else{
            echo "세션 저장 실패";
        }            
    }
    // 입력한 비밀번호가 틀린 경우
    else{
        echo "잘못된 아이디 또는 비밀번호입니다.";
        header('Location: ./index.html');
    }
}
// 사용자 정보가 존재하지 않는 경우
else{
    echo "잘못된 아이디 또는 비밀번호입니다.";
    header('Location: ./index.html');
}

$conn->close(); // 데이터베이스 연결 종료
?>
