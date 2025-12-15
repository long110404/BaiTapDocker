<?php
session_start();
include 'includes/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php"); 
    exit();
}

$id = intval($_GET['id']);

try {
    $sql = "SELECT * FROM books WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    if ($stmt->rowCount() == 0) {
        die("Sách không tồn tại!");
    }
    
    $book = $stmt->fetch();
} catch(PDOException $e) {
    die("Lỗi hệ thống!");
}

function get_image_path($filename) {
    if (empty($filename)) return "https://via.placeholder.com/400x600?text=No+Image";
    
    $folders = ['images/', 'image/', 'uploads/', 'admin/uploads/'];
    
    foreach ($folders as $folder) {
        if (file_exists($folder . $filename)) {
            return $folder . $filename; 
        }
    }
    return "https://via.placeholder.com/400x600?text=Loi+Anh"; 
}

$img_path = get_image_path($book['image']);

$description = isset($book['description']) ? $book['description'] : "Đang cập nhật nội dung...";
$author = isset($book['author']) ? $book['author'] : "Đang cập nhật";

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['title']; ?> - Chi Tiết Sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .price-tag { font-size: 2rem; color: #d63031; font-weight: bold; }
        .book-img { border: 1px solid #ddd; padding: 10px; border-radius: 10px; width: 100%; max-height: 500px; object-fit: contain; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">📚 BOOKSTORE</a>
    <div class="d-flex align-items-center">
        <?php if(isset($_SESSION['user_id'])): ?>
            <span class="text-white me-3">Chào, <b><?php echo $_SESSION['username']; ?></b></span>
        <?php endif; ?>

        <a href="cart.php" class="btn btn-warning position-relative me-2 fw-bold">
            <i class="fa fa-shopping-cart"></i> Giỏ hàng
            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo array_sum($_SESSION['cart']); ?>
                </span>
            <?php endif; ?>
        </a>
    </div>
  </div>
</nav>

<div class="container mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $book['title']; ?></li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-5 text-center">
                    <img src="<?php echo $img_path; ?>" alt="<?php echo $book['title']; ?>" class="book-img shadow-sm">
                </div>

                <div class="col-md-7">
                    <h1 class="fw-bold mb-2"><?php echo $book['title']; ?></h1>
                    <p class="text-muted">Tác giả: <span class="fw-bold text-dark"><?php echo $author; ?></span></p>
                    
                    <div class="price-tag mb-3">
                        <?php echo number_format($book['price'], 0, ',', '.'); ?> đ
                    </div>

                    <p>
                        Trạng thái: 
                        <?php if ($book['quantity'] > 0): ?>
                            <span class="badge bg-success">Còn hàng (<?php echo $book['quantity']; ?> cuốn)</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Hết hàng</span>
                        <?php endif; ?>
                    </p>

                    <hr>
                    
                    <div class="mb-4">
                        <h5 class="fw-bold">Mô tả sản phẩm:</h5>
                        <p class="text-secondary" style="line-height: 1.6;">
                            <?php echo nl2br($description); ?>
                        </p>
                    </div>

                    <?php if ($book['quantity'] > 0): ?>
                        <div class="p-3 bg-light rounded border">
                            <form action="add_cart.php" method="POST" class="d-flex align-items-end gap-3 flex-wrap">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                
                                <div style="width: 100px;">
                                    <label class="fw-bold small mb-1">Số lượng:</label>
                                    <input type="number" name="quantity" class="form-control text-center fw-bold" value="1" min="1" max="<?php echo $book['quantity']; ?>">
                                </div>
                                
                                <button type="submit" name="add_to_cart" class="btn btn-warning fw-bold flex-grow-1 text-dark">
                                    <i class="fa fa-cart-plus"></i> THÊM GIỎ
                                </button>

                                <button type="button" class="btn btn-danger fw-bold flex-grow-1" 
                                        onclick="openBuyModal(<?php echo $book['id']; ?>, '<?php echo $book['title']; ?>', <?php echo $book['price']; ?>)">
                                    <i class="fa fa-rocket"></i> MUA NGAY
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">Sản phẩm này tạm thời hết hàng.</div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="buyNowModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold">🚀 ĐẶT HÀNG NHANH</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="buy_now.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="book_id" id="modal_book_id">
            <input type="hidden" name="price" id="modal_book_price">
            
            <h5 id="modal_book_title" class="text-primary fw-bold mb-3 text-center"></h5>
            
            <div class="mb-3">
                <label class="fw-bold">Số lượng mua:</label>
                <input type="number" name="quantity" id="modal_quantity" class="form-control text-center fw-bold" value="1" min="1" required>
            </div>

            <hr>
            <p class="mb-2 fw-bold text-success">Thông tin giao hàng:</p>

            <div class="mb-2">
                <input type="text" name="fullname" class="form-control" placeholder="Họ và tên người nhận" 
                       value="<?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : ''; ?>" required>
            </div>
            <div class="mb-2">
                <input type="text" name="phone" class="form-control" placeholder="Số điện thoại" required>
            </div>
            <div class="mb-2">
                <textarea name="address" class="form-control" rows="2" placeholder="Địa chỉ nhận hàng..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-danger fw-bold">XÁC NHẬN MUA</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openBuyModal(id, title, price) {
        document.getElementById('modal_book_id').value = id;
        document.getElementById('modal_book_price').value = price;
        document.getElementById('modal_book_title').innerText = title;
        
        var userQty = document.querySelector('input[name="quantity"]').value;
        document.getElementById('modal_quantity').value = userQty;

        var myModal = new bootstrap.Modal(document.getElementById('buyNowModal'));
        myModal.show();
    }
</script>

</body>
</html>