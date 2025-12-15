<?php
session_start();
include 'check_login.php';

$name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : (isset($_SESSION['admin_user']) ? $_SESSION['admin_user'] : 'Admin');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khu Vực Quản Trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow text-center" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body p-5">
            <h2 class="text-primary fw-bold mb-3">KHU VỰC QUẢN TRỊ</h2>
            <p class="text-muted mb-4">Xin chào, <strong class="text-dark"><?php echo $name; ?></strong></p>

            <div class="d-grid gap-3">
                <a href="manage_books.php" class="btn btn-primary btn-lg">
                    Quản Lý Sách
                    <div class="small fw-normal">Thêm, Sửa, Xóa sách, Kho hàng</div>
                </a>

                <a href="manage_categories.php" class="btn btn-success btn-lg">
                    Quản Lý Thể Loại
                    <div class="small fw-normal">Thêm, Xóa danh mục</div>
                </a>

                <a href="manage_users.php" class="btn btn-warning text-white btn-lg">
                    Quản Lý Tài Khoản
                    <div class="small fw-normal">Danh sách, Phân quyền, Khóa user</div>
                </a>

                <a href="manage_orders.php" class="btn btn-info text-white btn-lg">
                     Quản Lý Đơn Hàng
                    <div class="small fw-normal">Xem đơn, In hóa đơn</div>
                </a>
            </div>

            <div class="mt-4">
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
                <a href="../index.php" class="btn btn-link btn-sm text-decoration-none">Về trang chủ bán hàng</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>