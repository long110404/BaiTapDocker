<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: admin/login.php"); exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    unset($_SESSION['cart']);
    header("Location: cart.php");
}

if (isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address']; 
    $total_price = $_POST['total_price'];

    if (empty($_SESSION['cart'])) {
        echo "<script>alert('Giỏ hàng trống!');</script>";
    } else {
        try {
            $sql_order = "INSERT INTO orders (user_id, fullname, phone, address, total_price, status) 
                          VALUES (:uid, :fn, :ph, :addr, :total, 'Pending')";
            $stmt = $conn->prepare($sql_order);
            
            if ($stmt->execute([
                ':uid' => $user_id,
                ':fn' => $fullname,
                ':ph' => $phone,
                ':addr' => $address,
                ':total' => $total_price
            ])) {
                $order_id = $conn->lastInsertId();

                foreach ($_SESSION['cart'] as $book_id => $qty) {
                    $stmt_price = $conn->prepare("SELECT price FROM books WHERE id = :bid");
                    $stmt_price->execute([':bid' => $book_id]);
                    $book = $stmt_price->fetch();
                    $price = $book['price'];

                    $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, book_id, quantity, price) 
                                                   VALUES (:oid, :bid, :qty, :price)");
                    $stmt_detail->execute([
                        ':oid' => $order_id,
                        ':bid' => $book_id,
                        ':qty' => $qty,
                        ':price' => $price
                    ]);
                    
                    $stmt_update = $conn->prepare("UPDATE books SET quantity = quantity - :qty WHERE id = :bid");
                    $stmt_update->execute([':qty' => $qty, ':bid' => $book_id]);
                }

                unset($_SESSION['cart']);
                echo "<script>alert('Đặt hàng thành công!'); window.location='index.php';</script>";
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
}

function get_img($filename) {
    $folders = ['images/', 'image/', 'uploads/', 'admin/uploads/'];
    foreach ($folders as $folder) {
        if (file_exists($folder . $filename)) return $folder . $filename; 
    }
    return "https://via.placeholder.com/100x150?text=No+Image"; 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <li class="nav-item text-white me-3">
                Xin chào, <b><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Khách'; ?></b>
            </li>
            <li class="nav-item">
                <a href="admin/logout.php" class="btn btn-outline-light btn-sm">Thoát</a>
            </li>
        </ul>
    </div>
  </div>
</nav>

<div class="container mb-5">
    <h3 class="mb-4 text-primary fw-bold"><i class="fa fa-shopping-cart"></i> Giỏ Hàng Của Bạn</h3>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-warning text-center p-5">
            <h4>Giỏ hàng đang trống!</h4>
            <p>Hãy chọn những cuốn sách hay để đọc nhé.</p>
            <a href="index.php" class="btn btn-primary fw-bold">← Quay lại mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 100px;">Hình ảnh</th>
                                        <th>Tên sách</th>
                                        <th>Giá</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total = 0;
                                    foreach ($_SESSION['cart'] as $id => $qty):
                                        $stmt_book = $conn->prepare("SELECT * FROM books WHERE id = :id");
                                        $stmt_book->execute([':id' => $id]);
                                        $row = $stmt_book->fetch();
                                        
                                        $subtotal = $row['price'] * $qty;
                                        $total += $subtotal;
                                        $img_src = get_img($row['image']);
                                    ?>
                                    <tr>
                                        <td><img src="<?php echo $img_src; ?>" style="height: 60px; object-fit: cover; border-radius: 5px;"></td>
                                        <td class="fw-bold"><?php echo $row['title']; ?></td>
                                        <td><?php echo number_format($row['price']); ?> đ</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?php echo $qty; ?></span>
                                        </td>
                                        <td class="text-end fw-bold text-primary"><?php echo number_format($subtotal); ?> đ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold text-uppercase">Tổng cộng:</td>
                                        <td class="fw-bold text-danger fs-5 text-end"><?php echo number_format($total); ?> đ</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Mua thêm</a>
                    <a href="?action=clear" class="btn btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa hết giỏ hàng?')">Xóa giỏ hàng</a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-success text-white fw-bold text-uppercase">
                        <i class="fa fa-truck"></i> Thông tin giao hàng
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="total_price" value="<?php echo $total; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Người nhận</label>
                                <input type="text" name="fullname" class="form-control" 
                                       value="<?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" required placeholder="09xxxxxxx">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-danger">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="3" required placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="checkout" class="btn btn-primary btn-lg fw-bold">
                                    XÁC NHẬN ĐẶT HÀNG
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>