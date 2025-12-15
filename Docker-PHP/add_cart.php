<?php
session_start();

if (isset($_POST['add_to_cart']) || isset($_POST['buy_now'])) {
    
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Bạn cần đăng nhập để mua hàng!'); window.location.href='admin/login.php';</script>";
        exit();
    }

    $id = intval($_POST['book_id']);
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($qty <= 0) $qty = 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }

    if (isset($_POST['buy_now'])) {
        header("Location: cart.php");
    } else {
        echo "<script>
                alert('Đã thêm sản phẩm vào giỏ hàng!');
                window.history.back();
              </script>";
    }
} else {
    header("Location: index.php");
}
?>