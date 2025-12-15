<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập để đặt hàng!'); window.location='admin/login.php';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $book_id = intval($_POST['book_id']);
    $qty = intval($_POST['quantity']);
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $price = $_POST['price'];
    
    $total_price = $price * $qty;

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

            $sql_detail = "INSERT INTO order_details (order_id, book_id, quantity, price) 
                           VALUES (:oid, :bid, :qty, :price)";
            $stmt_detail = $conn->prepare($sql_detail);
            $stmt_detail->execute([
                ':oid' => $order_id,
                ':bid' => $book_id,
                ':qty' => $qty,
                ':price' => $price
            ]);

            $sql_update = "UPDATE books SET quantity = quantity - :qty WHERE id = :bid";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([
                ':qty' => $qty,
                ':bid' => $book_id
            ]);

            echo "<script>alert('Đặt hàng thành công! Mã đơn: #$order_id'); window.location='index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>