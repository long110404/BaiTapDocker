<?php
include 'check_staff.php';
include '../includes/db.php';

if (!isset($_GET['id'])) { header("Location: manage_orders.php"); exit(); }
$order_id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $status = $_POST['status'];
     
        $note = isset($_POST['note']) ? $_POST['note'] : ''; 
        
        try {
            
            $stmt = $conn->prepare("UPDATE orders SET status = :st, note = :nt WHERE id = :id");
            $stmt->execute([':st' => $status, ':nt' => $note, ':id' => $order_id]);
            
            echo "<script>alert('Cập nhật thành công!'); window.location.href='order_detail_staff.php?id=$order_id';</script>";
        } catch(PDOException $e) {
            try {
                $stmt = $conn->prepare("UPDATE orders SET status = :st WHERE id = :id");
                $stmt->execute([':st' => $status, ':id' => $order_id]);
                echo "<script>alert('Cập nhật trạng thái thành công!'); window.location.href='order_detail_staff.php?id=$order_id';</script>";
            } catch(PDOException $ex) {
                echo "<script>alert('Lỗi cập nhật!');</script>";
            }
        }
    }
}

try {
   
    $stmt_order = $conn->prepare("SELECT orders.*, users.fullname, users.email, users.username 
                                  FROM orders 
                                  JOIN users ON orders.user_id = users.id 
                                  WHERE orders.id = :id");
    $stmt_order->execute([':id' => $order_id]);
    $order = $stmt_order->fetch();

   
    $stmt_items = $conn->prepare("SELECT order_details.*, books.title 
                                  FROM order_details 
                                  JOIN books ON order_details.book_id = books.id 
                                  WHERE order_details.order_id = :id");
    $stmt_items->execute([':id' => $order_id]);
    $items = $stmt_items->fetchAll();

} catch(PDOException $e) {
    die("Lỗi kết nối dữ liệu.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Chi Tiết Đơn #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none !important; } } 
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between mb-3 no-print">
            <a href="manage_orders.php" class="btn btn-secondary">← Quay lại</a>
            <button onclick="window.print()" class="btn btn-success">🖨️ In Phiếu Giao Hàng</button>
        </div>

        <div class="card p-4 shadow-sm">
            <div class="row border-bottom pb-3 mb-3">
                <div class="col-6">
                    <h4>PHIẾU GIAO HÀNG</h4>
                    <p class="mb-1">Mã đơn: <strong>#<?php echo $order['id']; ?></strong></p>
                    <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                </div>
                <div class="col-6 text-end">
                    <h5>Người nhận: <?php echo $order['fullname']; ?></h5>
                    <p class="mb-1">SĐT/Email: <?php echo $order['email']; ?></p>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Sản phẩm</th><th class="text-center">SL</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['title']; ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                        <td class="text-end"><?php echo number_format($item['price']); ?> đ</td>
                        <td class="text-end fw-bold"><?php echo number_format($item['price'] * $item['quantity']); ?> đ</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TỔNG CỘNG:</td>
                        <td class="text-end fw-bold text-danger fs-5"><?php echo number_format($order['total_price']); ?> đ</td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-4 p-3 bg-warning-subtle rounded no-print">
                <form method="POST">
                    
                    <div class="d-flex align-items-center gap-3">
                        <label class="fw-bold">Trạng thái:</label>
                        <select name="status" class="form-select w-auto">
                            <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Chờ xác nhận</option>
                            <option value="Shipping" <?php if($order['status']=='Shipping') echo 'selected'; ?>>Đang giao hàng</option>
                            <option value="Completed" <?php if($order['status']=='Completed') echo 'selected'; ?>>Hoàn thành</option>
                            <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Đã hủy</option>
                        </select>
                        
                        <button type="submit" name="update_status" class="btn btn-primary">Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>