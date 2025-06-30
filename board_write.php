<?php
session_start();

// 로그인 체크
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$conn = new mysqli("kknock6.mysql.database.azure.com", "hackers458", "swjisj123!", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'] ?? '자유게시판';
    $user_id = $_SESSION['user_id'];

    // 게시글 등록
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $content, $category);

    if ($stmt->execute()) {
        $post_id = $conn->insert_id;

        // 파일 업로드 처리
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . "/uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $tmp_name = $_FILES['upload_file']['tmp_name'];
            $original_name = basename($_FILES['upload_file']['name']);
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $destination)) {
                $stmt_file = $conn->prepare("INSERT INTO files (post_id, filename, original_name) VALUES (?, ?, ?)");
                $stmt_file->bind_param("iss", $post_id, $filename, $original_name);
                $stmt_file->execute();
                $stmt_file->close();
            } else {
                echo "파일 저장에 실패했습니다.";
            }
        }

        header("Location: board_list.php?category=" . urlencode($category));
        exit;
    } else {
        echo "게시글 작성 실패: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8" />
    <title>게시글 작성</title>
</head>
<body>
    <h2>게시글 작성</h2>
    <form method="post" enctype="multipart/form-data">
        <label>게시판:
            <select name="category" required>
                <option value="자유게시판">자유게시판</option>
                <option value="가입인사">가입인사</option>
            </select>
        </label><br><br>
        <label>제목: <input type="text" name="title" required></label><br><br>
        <label>내용:<br>
            <textarea name="content" rows="10" cols="50" required></textarea>
        </label><br><br>
        <label>첨부파일: <input type="file" name="upload_file"></label><br><br>
        <button type="submit">작성 완료</button>
    </form>
    <p><a href="board_list.php">목록으로</a></p>
</body>
</html>
