

<?php

session_start();


// 게시글 ID 유효성 검사
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "잘못된 접근입니다.";
    exit;
}

$post_id = (int)$_GET['id'];

// DB 연결

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 게시글 정보 조회
$stmt = $conn->prepare("
    SELECT posts.title, posts.content, posts.created_at, posts.user_id, users.nickname
    FROM posts 
    JOIN users ON posts.user_id = users.id
    WHERE posts.id = ?
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($title, $content, $created_at, $post_user_id, $post_nickname);
if (!$stmt->fetch()) {
    echo "게시글이 존재하지 않습니다.";
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
</head>
<body>
    <h2><?= htmlspecialchars($title) ?></h2>
    <p>작성자: <?= htmlspecialchars($post_nickname) ?> | 작성일: <?= $created_at ?></p>
    <hr>
    <p><?= nl2br(htmlspecialchars($content)) ?></p>
    




<?php
// 첨부파일 출력 시작
echo "<hr>";
echo "<h3>첨부파일</h3>";

$stmt_files = $conn->prepare("SELECT id, filename, original_name FROM files WHERE post_id = ?");
if (!$stmt_files) {
    echo "파일 조회 prepare 실패: " . $conn->error;
} else {
    $stmt_files->bind_param("i", $post_id);
    $stmt_files->execute();
    $result_files = $stmt_files->get_result();

    if ($result_files === false) {
        echo "파일 조회 get_result 실패";
    } elseif ($result_files->num_rows === 0) {
        echo "<p>첨부파일이 없습니다.</p>";
    } else {
        while ($file = $result_files->fetch_assoc()) {
            echo "<p>📎 <a href='uploads/" . htmlspecialchars($file['filename']) . "' download='" . htmlspecialchars($file['original_name']) . "'>" . 
                htmlspecialchars($file['original_name']) . "</a>";

            if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$post_user_id) {
                echo " | <a href='delete_file.php?id=" . (int)$file['id'] . "' onclick='return confirm(\"파일을 삭제하시겠습니까?\");'>삭제</a>";
            }

            echo "</p>";
        }
    }
    $stmt_files->close();
}
?>































    <p>
        <a href="board_list.php">목록으로</a> |
        <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$post_user_id): ?>
            <a href="board_edit.php?id=<?= $post_id ?>">수정</a> |
            <a href="board_delete.php?id=<?= $post_id ?>" onclick="return confirm('정말 삭제하시겠습니까?');">삭제</a>
        <?php endif; ?>
    </p>

 
    

    

    <hr>
    <h3>댓글 작성</h3>
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="add_comment.php" method="post">
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <textarea name="content" rows="4" cols="50" required></textarea><br>
            <input type="submit" value="댓글 달기">
        </form>
    <?php else: ?>
        <p>댓글을 작성하려면 <a href="index.html">로그인</a>이 필요합니다.</p>
    <?php endif; ?>

    <hr>
    <h3>댓글 목록</h3>
    <?php
    $stmt = $conn->prepare("
        SELECT c.id, c.content, c.created_at, c.user_id AS comment_user_id, u.nickname
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<p><strong>" . htmlspecialchars($row['nickname']) . "</strong>: " .
             nl2br(htmlspecialchars($row['content'])) . "<br><small>" .
             $row['created_at'] . "</small>";

        // 댓글 삭제 & 수정 권한: 댓글 작성자만 가능
        if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$row['comment_user_id']) {
            echo " | <a href='edit_comment.php?id=" . (int)$row['id'] . "'>수정</a>";
            echo " | <a href='delete_comment.php?id=" . (int)$row['id'] . "' onclick='return confirm(\"댓글을 삭제하시겠습니까?\");'>삭제</a>";
        }

        echo "</p><hr>";
    }
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
