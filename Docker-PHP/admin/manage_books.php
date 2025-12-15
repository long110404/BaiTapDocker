<?php
include 'check_login.php';
include '../includes/db.php';

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    try {
        $stmt = $conn->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header("Location: manage_books.php");
        exit();
    } catch(PDOException $e) {
        echo "<script>alert('Lỗi xóa sách!');</script>";
    }
}

$search = "";
$sql = "SELECT books.*, categories.name AS category_name 
        FROM books 
        LEFT JOIN categories ON books.category_id = categories.id 
        WHERE 1=1";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " AND (books.title LIKE :s OR books.author LIKE :s)";
    $params[':s'] = "%$search%";
}

$sql .= " ORDER BY books.id DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
} catch(PDOException $e) {
    $books = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Kho Sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản Lý Kho Sách</h2>
            <a href="add_book.php" class="btn btn-success">+ Nhập Sách Mới</a>
        </div>

        <div class="card p-3 mb-4 shadow-sm">
            <form action="" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Nhập tên sách hoặc tác giả để tìm..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 Tìm kiếm</button>
                </div>
            </form>
            <?php if($search): ?>
                <div class="mt-2">
                    <small>Đang hiện kết quả cho: <strong><?php echo htmlspecialchars($search); ?></strong></small> 
                    <a href="manage_books.php" class="text-danger ms-2" style="text-decoration:none;">(Xóa lọc)</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card shadow-sm">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên Sách</th>
                        <th>Thể loại</th>
                        <th>Giá bán</th>
                        <th>Tồn kho</th>
                        <th>Tác giả</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($books) > 0): ?>
                        <?php foreach($books as $row): 
                            $stock_class = ($row['quantity'] < 5) ? 'text-danger fw-bold' : 'text-success';
                            $cate_name = $row['category_name'] ? $row['category_name'] : '<span class="text-muted text-sm">Chưa phân loại</span>';
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><img src="../uploads/<?php echo $row['image']; ?>" width="50" class="rounded border"></td>
                            <td class="fw-bold"><?php echo $row['title']; ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo $cate_name; ?></span></td>
                            <td><?php echo number_format($row['price']); ?> đ</td>
                            <td class="<?php echo $stock_class; ?>"><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['author']; ?></td>
                            <td>
                                <a href="edit_book.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Sửa</a>
                                <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sách này?')">Xóa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center p-4">Không tìm thấy cuốn sách nào!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
             <a href="index.php" class="btn btn-secondary">← Quay lại Menu</a>
        </div>
    </div>
</body>
</html>