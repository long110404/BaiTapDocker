<?php
session_start();
include 'check_login.php';
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    if (!empty($name)) {
        try {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->execute([':name' => $name]);
            echo "<script>alert('Thêm thành công!');</script>";
        } catch(PDOException $e) {
            echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header("Location: manage_categories.php");
        exit();
    } catch(PDOException $e) {
        echo "<script>alert('Không thể xóa danh mục này (có thể đang chứa sách)!'); window.location.href='manage_categories.php';</script>";
    }
}

$cats = [];
try {
    $stmt = $conn->query("SELECT * FROM categories");
    $cats = $stmt->fetchAll();
} catch(PDOException $e) {
    $cats = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Danh Mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Quản Lý Danh Mục Sách</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Thêm Danh Mục Mới</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Tên danh mục..." required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Thêm Mới</button>
                    </form>
                </div>
                <div class="mt-3">
                    <a href="index.php" class="btn btn-outline-secondary w-100">← Quay lại Menu chính</a>
                </div>
            </div>

            <div class="col-md-8">
                <table class="table table-bordered bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên Danh Mục</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($cats) > 0): ?>
                            <?php foreach ($cats as $c): ?>
                            <tr>
                                <td><?php echo $c['id']; ?></td>
                                <td><?php echo $c['name']; ?></td>
                                <td>
                                    <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">Xóa</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">Chưa có danh mục nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>