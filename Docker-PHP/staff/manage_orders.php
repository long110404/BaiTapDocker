<?php
include 'check_staff.php';
include '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Xử Lý Đơn Hàng</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h3>📦 Danh Sách Đơn Hàng (Nhân Viên)</h3>
            <a href="index.php" class="btn btn-secondary">← Quay lại Menu</a>
        </div>
        
        <div class="card shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT orders.*, users.fullname 
                            FROM orders 
                            LEFT JOIN users ON orders.user_id = users.id 
                            ORDER BY orders.id DESC";
                    
                    try {
                        $stmt = $conn->query($sql);
                        $orders = $stmt->fetchAll();
                    } catch(PDOException $e) {
                        $orders = [];
                    }

                    foreach ($orders as $row) {
                        $status_map = [
                            'Pending' => ['Chờ xác nhận', 'bg-warning text-dark'],
                            'Shipping' => ['Đang giao', 'bg-primary'],
                            'Completed' => ['Hoàn thành', 'bg-success'],
                            'Cancelled' => ['Đã hủy', 'bg-danger']
                        ];
                        
                        $st = isset($status_map[$row['status']]) ? $status_map[$row['status']] : [$row['status'], 'bg-secondary'];

                        echo "<tr>";
                        echo "<td>#".$row['id']."</td>";
                        echo "<td>".$row['fullname']."</td>";
                        echo "<td>".date('d/m H:i', strtotime($row['created_at']))."</td>";
                        echo "<td class='fw-bold'>".number_format($row['total_price'])." đ</td>";
                        echo "<td><span class='badge ".$st[1]."'>".$st[0]."</span></td>";
                        echo "<td>
                                <a href='order_detail_staff.php?id=".$row['id']."' class='btn btn-sm btn-primary'>Xử lý / In phiếu</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>