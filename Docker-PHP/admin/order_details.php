<?php
include 'check_login.php';
include '../includes/db.php';

if (!isset($_GET['id'])) { header("Location: manage_orders.php"); exit(); }
$order_id = intval($_GET['id']);

if (isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = :st WHERE id = :id");
        $stmt->execute([':st' => $new_status, ':id' => $order_id]);
        echo "<script>alert('Đã cập nhật trạng thái!'); window.location.href='order_details.php?id=$order_id';</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
}

try {
    $stmt = $conn->prepare("SELECT orders.*, users.fullname, users.email 
                            FROM orders 
                            LEFT JOIN users ON orders.user_id = users.id 
                            WHERE orders.id = :id");
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch();

    if (!$order) die("Không tìm thấy đơn hàng!");

    $stmt_items = $conn->prepare("SELECT order_details.*, books.title 
                                  FROM order_details 
                                  JOIN books ON order_details.book_id = books.id 
                                  WHERE order_details.order_id = :id");
    $stmt_items->execute([':id' => $order_id]);
    $items = $stmt_items->fetchAll();

} catch(PDOException $e) {
    die("Lỗi hệ thống!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> @media print { .no-print { display: none; } } </style>
</head>
<body class="bg-light">
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between mb-3 no-print">
            <a href="manage_orders.php" class="btn btn-secondary">← Quay lại</a>
            <button onclick="window.print()" class="btn btn-success">🖨️ In Hóa Đơn</button>
        </div>

        <div class="card p-4 shadow-sm">
            <h4 class="text-primary text-center mb-4">HÓA ĐƠN BÁN HÀNG</h4>
            <div class="row border-bottom pb-3 mb-3">
                <div class="col-6">
                    <p>Mã đơn: <strong>#<?php echo $order['id']; ?></strong></p>
                    <p>Ngày đặt: <?php echo $order['created_at']; ?></p>
                    <p>Trạng thái: <span class="badge bg-info text-dark"><?php echo $order['status']; ?></span></p>
                </div>
                <div class="col-6 text-end">
                    <h5>Người nhận hàng</h5>
                    <p class="mb-1"><strong><?php echo $order['fullname']; ?></strong></p>
                    <p class="mb-1">SĐT: <?php echo $order['phone']; ?></p>
                    <p class="mb-1">Địa chỉ: <?php echo $order['address']; ?></p>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr class="table-light"><th>Sản phẩm</th><th class="text-center">SL</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $final_total = 0;
                    if (count($items) > 0):
                        foreach($items as $item): 
                            $sub = $item['price'] * $item['quantity'];
                            $final_total += $sub;
                    ?>
                    <tr>
                        <td><?php echo $item['title']; ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                        <td class="text-end"><?php echo number_format($item['price']); ?></td>
                        <td class="text-end fw-bold"><?php echo number_format($sub); ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="text-end fw-bold">TỔNG CỘNG:</td><td class="text-end fw-bold text-danger"><?php echo number_format($final_total); ?> đ</td></tr>
                </tfoot>
            </table>

            <div class="mt-4 p-3 bg-white border no-print">
                <form method="POST" class="d-flex gap-2 align-items-center">
                    <label class="fw-bold">Xử lý đơn:</label>
                    <select name="status" class="form-select w-auto">
                        <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Chờ xác nhận</option>
                        <option value="Shipping" <?php if($order['status']=='Shipping') echo 'selected'; ?>>Đang giao</option>
                        <option value="Completed" <?php if($order['status']=='Completed') echo 'selected'; ?>>Hoàn thành</option>
                        <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Hủy bỏ</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-primary">Lưu trạng thái</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>