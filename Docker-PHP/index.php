<?php
session_start();
include 'includes/db.php'; 

$is_logged_in = isset($_SESSION['user_id']);

$sql = "SELECT * FROM books WHERE 1=1"; 
$params = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $sql .= " AND title LIKE :q"; 
    $params[':q'] = '%' . $_GET['q'] . '%';
}
if (isset($_GET['cate']) && !empty($_GET['cate'])) {
    $sql .= " AND category_id = :cate";
    $params[':cate'] = $_GET['cate'];
}
$sql .= " ORDER BY id DESC"; 

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll();
} catch(PDOException $e) {
    $books = [];
}

$categories = [];
try {
    $stmt_cat = $conn->query("SELECT * FROM categories");
    if ($stmt_cat) {
        $categories = $stmt_cat->fetchAll();
    }
} catch(PDOException $e) {
    $categories = [];
}

function get_image_path($filename) {
    if (empty($filename)) return "https://via.placeholder.com/300x400?text=No+Image";
    $folders = ['images/', 'image/', 'uploads/', 'admin/uploads/'];
    foreach ($folders as $folder) {
        if (file_exists($folder . $filename)) return $folder . $filename; 
    }
    return "https://via.placeholder.com/300x400?text=Loi+Anh"; 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhà Sách Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .book-card { background: #fff; border-radius: 8px; border: 1px solid #eee; transition: 0.3s; height: 100%; display: flex; flex-direction: column; }
        .book-img-container { height: 200px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; padding: 10px; border-bottom: 1px solid #eee; }
        @media (max-width: 768px) {
            .book-img-container { height: 160px; } 
        }
        .book-img-container img { max-height: 100%; max-width: 100%; object-fit: contain; }
        .book-info { padding: 10px; flex-grow: 1; display: flex; flex-direction: column; text-align: center; }
        .book-title { font-size: 1rem; font-weight: 700; color: #333; margin-bottom: 5px; height: 2.4rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;}
        .book-price { color: #d63031; font-size: 1.1rem; font-weight: bold; margin: 5px 0; }
        .btn-sm-mobile { font-size: 0.8rem; padding: 5px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 sticky-top shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">📚 BOOKSTORE</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center mt-2 mt-lg-0">
            <li class="nav-item d-lg-none w-100 mb-2">
                <form action="index.php" method="GET" class="d-flex">
                    <input type="text" name="q" class="form-control me-2" placeholder="Tìm sách...">
                    <button class="btn btn-primary" type="submit">🔍</button>
                </form>
            </li>

            <?php if ($is_logged_in): ?>
                <li class="nav-item text-white me-3 mb-2 mb-lg-0">Chào, <b><?php echo $_SESSION['username']; ?></b></li>
                
                <?php 
                if(isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                    <li class="nav-item mb-2 mb-lg-0 me-2"><a href="admin/index.php" class="btn btn-danger btn-sm w-100"><i class="fa fa-cogs"></i> Admin</a></li>
                
                <?php 
                elseif(isset($_SESSION['role']) && $_SESSION['role'] == 2): ?>
                    <li class="nav-item mb-2 mb-lg-0 me-2"><a href="staff/index.php" class="btn btn-success btn-sm w-100"><i class="fa fa-user-tie"></i> Nhân viên</a></li>
                <?php endif; ?>

                <li class="nav-item mb-2 mb-lg-0 me-2">
                    <a href="cart.php" class="btn btn-warning btn-sm fw-bold w-100 position-relative">
                        <i class="fa fa-shopping-cart"></i> Giỏ hàng
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="badge rounded-pill bg-danger ms-1"><?php echo array_sum($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item me-2 mb-2 mb-lg-0">
                    <a href="history.php" class="btn btn-info btn-sm text-white w-100 fw-bold">
                        <i class="fa fa-history"></i> Lịch sử đơn
                    </a>
                </li>
                <li class="nav-item"><a href="admin/logout.php" class="btn btn-outline-light btn-sm w-100">Thoát</a></li>
            <?php else: ?>
                <li class="nav-item"><a href="admin/login.php" class="btn btn-primary btn-sm w-100">Đăng nhập</a></li>
            <?php endif; ?>
        </ul>
    </div>
  </div>
</nav>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold"><i class="fa fa-search"></i> TÌM KIẾM</div>
                <div class="card-body">
                    <form action="index.php" method="GET">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Tên sách..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                            <button class="btn btn-outline-primary" type="submit">🔍</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="list-group shadow-sm">
                <div class="list-group-item bg-dark text-white fw-bold"><i class="fa fa-list"></i> DANH MỤC</div>
                <a href="index.php" class="list-group-item list-group-item-action <?php echo !isset($_GET['cate']) ? 'active' : ''; ?>">Tất cả sách</a>
                <?php
                if (count($categories) > 0) {
                    foreach ($categories as $c) {
                        $act = (isset($_GET['cate']) && $_GET['cate'] == $c['id']) ? 'active bg-primary border-primary' : '';
                        echo "<a href='index.php?cate=".$c['id']."' class='list-group-item list-group-item-action $act'>".$c['name']."</a>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="col-lg-9 col-12">
            <div class="d-lg-none mb-3 overflow-auto text-nowrap pb-2">
                <a href="index.php" class="btn btn-outline-dark btn-sm rounded-pill <?php echo !isset($_GET['cate']) ? 'active' : ''; ?>">Tất cả</a>
                <?php
                if (count($categories) > 0) {
                    foreach ($categories as $c) {
                        $act = (isset($_GET['cate']) && $_GET['cate'] == $c['id']) ? 'btn-primary text-white' : 'btn-outline-primary';
                        echo "<a href='index.php?cate=".$c['id']."' class='btn btn-sm rounded-pill ms-1 $act'>".$c['name']."</a>";
                    }
                }
                ?>
            </div>

            <h5 class="text-primary fw-bold border-bottom pb-2 mb-3">
                <?php echo isset($_GET['q']) ? "Kết quả tìm kiếm" : "Sách Mới Nhất"; ?>
            </h5>

            <div class="row gx-2 gy-3"> 
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $row): 
                        $img_src = get_image_path($row['image']);
                    ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="book-card">
                            <div class="book-img-container">
                                <img src="<?php echo $img_src; ?>" alt="<?php echo $row['title']; ?>">
                            </div>
                            <div class="book-info">
                                <div class="book-title" title="<?php echo $row['title']; ?>"><?php echo $row['title']; ?></div>
                                <div class="text-muted small" style="font-size: 0.8rem;">Kho: <b><?php echo $row['quantity']; ?></b></div>
                                <div class="book-price"><?php echo number_format($row['price'], 0, ',', '.'); ?> đ</div>
                                
                                <div class="mt-auto">
                                    <div class="d-grid gap-2">
                                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm-mobile">Chi tiết</a>
                                        
                                        <?php if($row['quantity'] > 0): ?>
                                            <button type="button" class="btn btn-danger btn-sm-mobile fw-bold" 
                                                    onclick="openBuyModal(<?php echo $row['id']; ?>, '<?php echo $row['title']; ?>', <?php echo $row['price']; ?>)">
                                                MUA NGAY
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm-mobile" disabled>Hết</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12"><div class="alert alert-warning text-center">Không tìm thấy sách nào!</div></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="buyNowModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"> <div class="modal-content">
      <div class="modal-header bg-danger text-white py-2">
        <h6 class="modal-title fw-bold">ĐẶT HÀNG NHANH</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="buy_now.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="book_id" id="modal_book_id">
            <input type="hidden" name="price" id="modal_book_price">
            
            <div class="text-center mb-3">
                <span class="badge bg-primary fs-6" id="modal_book_title"></span>
            </div>
            
            <div class="mb-2">
                <label class="small fw-bold">Số lượng:</label>
                <input type="number" name="quantity" class="form-control form-control-sm text-center fw-bold" value="1" min="1" required>
            </div>
            
            <div class="mb-2">
                <input type="text" name="fullname" class="form-control form-control-sm" placeholder="Họ tên" value="<?php echo isset($_SESSION['fullname']) ? $_SESSION['fullname'] : ''; ?>" required>
            </div>
            <div class="mb-2">
                <input type="tel" name="phone" class="form-control form-control-sm" placeholder="SĐT" required>
            </div>
            <div class="mb-2">
                <textarea name="address" class="form-control form-control-sm" rows="2" placeholder="Địa chỉ..." required></textarea>
            </div>
          </div>
          <div class="modal-footer py-1">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-danger btn-sm fw-bold">ĐẶT HÀNG</button>
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
        var myModal = new bootstrap.Modal(document.getElementById('buyNowModal'));
        myModal.show();
    }
</script>

</body>
</html>