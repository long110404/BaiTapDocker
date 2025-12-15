<?php
include 'check_staff.php';
include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

$id = intval($_GET['id']);

try {
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $book = $stmt->fetch();

    if (!$book) {
        die("Sách không tồn tại!");
    }
} catch(PDOException $e) {
    die("Lỗi hệ thống!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qty = intval($_POST['quantity']);
    
    try {
        $stmt_update = $conn->prepare("UPDATE books SET quantity = :qty WHERE id = :id");
        $stmt_update->execute([':qty' => $qty, ':id' => $id]);
        echo "<script>alert('Đã cập nhật số lượng tồn kho!'); window.location.href='manage_books.php';</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Nhập Kho</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card p-4 shadow">
            <h4 class="text-center mb-3">Nhập Hàng Vào Kho</h4>
            
            <form method="POST">
                <div class="mb-3">
                    <label>Tên sách (Không được sửa)</label>
                    <input type="text" class="form-control" value="<?php echo $book['title']; ?>" readonly style="background: #e9ecef;">
                </div>
                
                <div class="mb-3">
                    <label>Giá bán (Không được sửa)</label>
                    <input type="text" class="form-control" value="<?php echo number_format($book['price']); ?> đ" readonly style="background: #e9ecef;">
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-success">Số lượng tồn kho hiện tại</label>
                    <input type="number" name="quantity" class="form-control border-success" value="<?php echo $book['quantity']; ?>" required>
                    <small class="text-muted">Nhập tổng số lượng mới sau khi kiểm đếm.</small>
                </div>

                <button type="submit" class="btn btn-warning w-100">Lưu Số Lượng Mới</button>
                <a href="manage_books.php" class="btn btn-secondary w-100 mt-2">Hủy</a>
            </form>
        </div>
    </div>
</body>
</html>