<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: admin/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);
    
    try {
        $sql_check = "UPDATE orders SET status = 'Cancelled' 
                      WHERE id = :oid AND user_id = :uid AND status = 'Pending'";
        $stmt = $conn->prepare($sql_check);
        $stmt->execute([':oid' => $order_id, ':uid' => $user_id]);
        
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Đã hủy đơn hàng thành công!'); window.location.href='history.php';</script>";
        } else {
            echo "<script>alert('Lỗi: Không thể hủy đơn này!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi hệ thống!');</script>";
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC");
    $stmt->execute([':uid' => $user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Mua Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .order-card { border-left: 5px solid #0d6efd; transition: 0.3s; background: white; }
        .order-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-shipping { background-color: #17a2b8; color: #fff; }
        .badge-completed { background-color: #198754; color: #fff; }
        .badge-cancelled { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">📚 BOOKSTORE</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item text-white me-3">Chào, <b><?php echo $_SESSION['username']; ?></b></li>
            <li class="nav-item me-2"><a href="cart.php" class="btn btn-warning btn-sm fw-bold"><i class="fa fa-shopping-cart"></i> Giỏ hàng</a></li>
            <li class="nav-item"><a href="admin/logout.php" class="btn btn-outline-light btn-sm">Thoát</a></li>
        </ul>
    </div>
  </div>
</nav>

<div class="container mb-5">
    
    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Quay lại trang chủ
        </a>
    </div>

    <h3 class="mb-4 text-primary fw-bold border-bottom pb-2">
        <i class="fa fa-history"></i> Lịch Sử Đơn Hàng
    </h3>

    <?php if (count($orders) > 0): ?>
        <div class="row">
            <?php foreach ($orders as $order): ?>
                <div class="col-md-12 mb-3">
                    <div class="card order-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h5 class="fw-bold mb-1">Đơn hàng #<?php echo $order['id']; ?></h5>
                                    <small class="text-muted"><i class="fa fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                                </div>
                                <div class="text-end">
                                    <?php 
                                        $stt = $order['status'];
                                        $class = 'badge-pending';
                                        $text = 'Chờ xử lý';
                                        if($stt == 'Processing') { $class = 'badge-info text-dark'; $text = 'Đang đóng gói'; }
                                        if($stt == 'Shipping') { $class = 'badge-shipping'; $text = 'Đang giao'; }
                                        if($stt == 'Completed') { $class = 'badge-completed'; $text = 'Hoàn thành'; }
                                        if($stt == 'Cancelled') { $class = 'badge-cancelled'; $text = 'Đã hủy'; }
                                    ?>
                                    <span class="badge <?php echo $class; ?> fs-6 mb-1"><?php echo $text; ?></span>
                                    <h5 class="text-danger fw-bold mb-0"><?php echo number_format($order['total_price']); ?> đ</h5>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="bg-light p-2 rounded mb-3">
                                <small class="fw-bold text-secondary">Sản phẩm đã mua:</small>
                                <ul class="mb-0 ps-3">
                                    <?php
                                    $stmt_detail = $conn->prepare("SELECT b.title, od.quantity FROM order_details od JOIN books b ON od.book_id = b.id WHERE od.order_id = :oid");
                                    $stmt_detail->execute([':oid' => $order['id']]);
                                    $details = $stmt_detail->fetchAll();
                                    
                                    foreach ($details as $d):
                                    ?>
                                        <li class="small">
                                            <?php echo $d['title']; ?> 
                                            <span class="text-muted">(x<?php echo $d['quantity']; ?>)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="fa fa-map-marker-alt"></i> Giao đến: <?php echo $order['address']; ?>
                                </div>

                                <?php if($order['status'] == 'Pending'): ?>
                                    <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <button type="submit" name="cancel_order" class="btn btn-outline-danger btn-sm fw-bold">
                                            <i class="fa fa-times"></i> Hủy đơn hàng
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center p-5">
            <h4>Bạn chưa có đơn hàng nào!</h4>
            <a href="index.php" class="btn btn-primary mt-3">Mua sắm ngay</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>