<?php
include 'check_staff.php';
include '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Kho Sách</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h3> Kiểm Tra Kho Sách</h3>
            <a href="index.php" class="btn btn-secondary">← Quay lại Menu</a>
        </div>

        <div class="card shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-warning">
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên Sách</th>
                        <th>Giá bán</th>
                        <th>Tồn kho</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $conn->query("SELECT * FROM books ORDER BY quantity ASC");
                        $books = $stmt->fetchAll();
                    } catch(PDOException $e) {
                        $books = [];
                    }

                    foreach($books as $row) {
                        $stock_alert = ($row['quantity'] < 10) ? 'text-danger fw-bold' : 'text-success';
                        
                        echo "<tr>";
                        echo "<td>".$row['id']."</td>";
                        echo "<td><img src='../uploads/".$row['image']."' width='50'></td>";
                        echo "<td>".$row['title']."</td>";
                        echo "<td>".number_format($row['price'])." đ</td>";
                        echo "<td class='$stock_alert'>".$row['quantity']."</td>";
                        echo "<td>
                                <a href='edit_stock.php?id=".$row['id']."' class='btn btn-sm btn-warning'>Cập nhật kho</a>
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