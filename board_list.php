<?php
session_start();
require_once 'auth_check.php';

$conn = new mysqli("localhost", "root", "swjisj123!B", "user_info");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 파라미터 수집
$category = $_GET['category'] ?? '자유게시판';
$search_title = $_GET['search_title'] ?? '';
$search_writer = $_GET['search_writer'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

// SQL 쿼리 생성
$query = "
    SELECT posts.id, posts.title, posts.created_at, users.nickname
    FROM posts
    JOIN users ON posts.user_id = users.id
    WHERE posts.category = ?
";

$params = [$category];
$types = 's';

if ($search_title !== '') {
    $query .= " AND posts.title LIKE ?";
    $params[] = '%' . $search_title . '%';
    $types .= 's';
}

if ($search_writer !== '') {
    $query .= " AND users.nickname LIKE ?";
    $params[] = '%' . $search_writer . '%';
    $types .= 's';
}

// 정렬 조건
switch ($sort) {
    case 'oldest':
        $query .= " ORDER BY posts.created_at ASC";
        break;
    case 'title':
        $query .= " ORDER BY posts.title ASC";
        break;
    default:
        $query .= " ORDER BY posts.created_at DESC";
        break;
}

// prepare, bind, execute
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category) ?> 게시판</title>
</head>
<body>
    <h2><?= htmlspecialchars($category) ?> 게시판</h2>

    <!-- 카테고리 탭 -->
    <p>
        <a href="?category=자유게시판" <?= $category === '자유게시판' ? 'style="font-weight:bold;"' : '' ?>>자유게시판</a> |
        <a href="?category=가입인사" <?= $category === '가입인사' ? 'style="font-weight:bold;"' : '' ?>>가입인사</a>
    </p>

    <!-- 검색 및 정렬 -->
    <form method="get" style="margin-bottom: 10px;">
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        제목: <input type="text" name="search_title" value="<?= htmlspecialchars($search_title) ?>">
        작성자: <input type="text" name="search_writer" value="<?= htmlspecialchars($search_writer) ?>">
        정렬:
        <select name="sort">
            <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>최신순</option>
            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>오래된순</option>
            <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>제목순</option>
        </select>
        <button type="submit">검색</button>
    </form>

    <p><a href="board_write.php?category=<?= urlencode($category) ?>">글쓰기</a> | <a href="logout.php">로그아웃</a></p>

    <table border="1" cellpadding="5">
        <tr>
            <th>제목</th>
            <th>작성자</th>
            <th>작성일</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><a href="board_view.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></td>
                    <td><?= htmlspecialchars($row['nickname']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">검색 결과가 없습니다.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
