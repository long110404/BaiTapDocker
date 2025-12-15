<?php
include 'check_login.php';
include '../includes/db.php';

// Lấy danh sách đơn hàng (PDO)
// Sử dụng LEFT JOIN để lấy thông tin người đặt, nếu user bị xóa vẫn hiện đơn
// Ưu tiên lấy tên người nhận trong đơn hàng (orders.fullname)
$sql = "SELECT orders.*, users.fullname as user_account_name 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        ORDER BY orders.id DESC";

try {
    $stmt = $conn->query($sql);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
    echo "<script>alert('Lỗi tải danh sách đơn hàng!');</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản Lý Đơn Hàng</h2>
            <a href="index.php" class="btn btn-secondary">← Quay lại Menu</a>
        </div>
        
        <div class="card shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Người Nhận</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $row): 
                            $status = $row['status'];
                            $badge_color = 'bg-secondary';
                            $status_text = $status;

                            // Xử lý màu sắc trạng thái
                            switch ($status) {
                                case 'Pending': 
                                    $badge_color = 'bg-warning text-dark'; $status_text = 'Chờ xử lý'; break;
                                case 'Processing': 
                                    $badge_color = 'bg-info text-dark'; $status_text = 'Đang đóng gói'; break;
                                case 'Shipping': 
                                    $badge_color = 'bg-primary'; $status_text = 'Đang giao hàng'; break;
                                case 'Completed': 
                                    $badge_color = 'bg-success'; $status_text = 'Hoàn thành'; break;
                                case 'Cancelled': 
                                    $badge_color = 'bg-danger'; $status_text = 'Đã hủy'; break;
                            }
                        ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <b><?php echo $row['fullname']; ?></b><br>
                                <small class="text-muted">TK: <?php echo $row['user_account_name']; ?></small>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td class="fw-bold text-danger"><?php echo number_format($row['total_price']); ?> đ</td>
                            <td><span class="badge <?php echo $badge_color; ?>"><?php echo $status_text; ?></span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="alert('Chức năng xem chi tiết đang phát triển hoặc chưa có file order_details.php')">Xem Chi Tiết</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center p-4">Chưa có đơn hàng nào!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>