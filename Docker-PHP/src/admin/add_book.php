<?php
include 'check_login.php';
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity']; 
    $category_id = $_POST['category_id'];
    $publisher = $_POST['publisher']; 
    $publish_year = $_POST['publish_year']; 
    $description = $_POST['description'];
    
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);
    
    try {
        $sql = "INSERT INTO books (title, author, price, quantity, category_id, publisher, publish_year, description, image) 
                VALUES (:t, :a, :p, :q, :c, :pub, :py, :desc, :img)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([
            ':t' => $title,
            ':a' => $author,
            ':p' => $price,
            ':q' => $quantity,
            ':c' => $category_id,
            ':pub' => $publisher,
            ':py' => $publish_year,
            ':desc' => $description,
            ':img' => $image
        ])) {
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
            echo "<div class='alert alert-success'>Thêm sách thành công! <a href='manage_books.php'>Về danh sách</a></div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sách Chi Tiết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4 mb-5">
        <h3 class="text-center mb-4">Nhập Sách Mới</h3>
        
        <div class="card p-4 shadow-sm">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Tên sách</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Tác giả</label>
                        <input type="text" name="author" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Giá bán (VNĐ)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-danger">Số lượng tồn kho</label>
                        <input type="number" name="quantity" class="form-control" value="10" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Nhà xuất bản</label>
                        <input type="text" name="publisher" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Năm xuất bản</label>
                        <input type="number" name="publish_year" class="form-control" placeholder="VD: 2024">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Thể loại</label>
                        <select name="category_id" class="form-select">
                            <?php
                            $cats = $conn->query("SELECT * FROM categories")->fetchAll();
                            foreach($cats as $c) {
                                echo "<option value='".$c['id']."'>".$c['name']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Ảnh bìa</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Mô tả chi tiết</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Lưu Sách Kho</button>
            </form>
        </div>
    </div>
</body>
</html>