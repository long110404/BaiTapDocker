<?php
include 'check_staff.php'; 
include '../includes/db.php';

$name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Nhân viên');

$count_pending = 0;
$count_low_stock = 0;

try {
    $stmt1 = $conn->query("SELECT COUNT(*) FROM orders WHERE status='Pending'");
    $count_pending = $stmt1->fetchColumn();

    $stmt2 = $conn->query("SELECT COUNT(*) FROM books WHERE quantity < 10");
    $count_low_stock = $stmt2->fetchColumn();
} catch(PDOException $e) {
    $count_pending = 0;
    $count_low_stock = 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bàn Làm Việc Nhân Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .work-card { border-left: 5px solid #0d6efd; transition: 0.3s; }
        .work-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold"> NHÂN VIÊN</span>
        <div class="text-white">
            Xin chào: <strong><?php echo $name; ?></strong>
            <a href="../admin/logout.php" class="btn btn-sm btn-light text-success fw-bold ms-2">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container">
    
    <div class="alert alert-warning shadow-sm border-0">
        <h5>Việc cần làm ngay:</h5>
        <ul class="mb-0">
            <li>Bạn có <strong><?php echo $count_pending; ?></strong> đơn hàng mới cần xác nhận.</li>
            <li>Có <strong><?php echo $count_low_stock; ?></strong> đầu sách sắp hết hàng.</li>
        </ul>
    </div>

    <h5 class="text-muted mt-4 mb-3">MENU CHỨC NĂNG</h5>
    <div class="row">
        
        <div class="col-md-6 mb-3">
            <div class="card work-card h-100 p-3">
                <h5 class="text-primary fw-bold"> Quản Lý Đơn Hàng</h5>
                <p class="text-muted small">Xem đơn, in phiếu giao hàng, cập nhật trạng thái.</p>
                <a href="manage_orders.php" class="btn btn-primary w-100">Xử Lý Đơn</a>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card work-card h-100 p-3" style="border-color: #ffc107;">
                <h5 class="text-warning fw-bold"> Kiểm Tra Kho</h5>
                <p class="text-muted small">Xem tồn kho, nhập số lượng sách mới về.</p>
                <a href="manage_books.php" class="btn btn-warning text-white w-100">Vào Kho</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>