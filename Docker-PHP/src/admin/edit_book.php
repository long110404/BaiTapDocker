<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] == 2) {
    header("Location: ../staff/index.php");
    exit();
}

include 'check_login.php';
include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $book = $stmt->fetch();
} else {
    header("Location: manage_books.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity']; 
    $category_id = $_POST['category_id'];
    $publisher = $_POST['publisher']; 
    $publish_year = $_POST['publish_year']; 
    $description = $_POST['description'];
    
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . basename($image));
    } else {
        $image = $_POST['old_image'];
    }

    try {
        $sql = "UPDATE books SET title=:t, author=:a, price=:p, quantity=:q, 
                category_id=:c, publisher=:pub, publish_year=:py, 
                description=:desc, image=:img WHERE id=:id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':t' => $title,
            ':a' => $author,
            ':p' => $price,
            ':q' => $quantity,
            ':c' => $category_id,
            ':pub' => $publisher,
            ':py' => $publish_year,
            ':desc' => $description,
            ':img' => $image,
            ':id' => $id
        ]);

        echo "<script>alert('Cập nhật thành công!'); window.location.href='manage_books.php';</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h3 class="text-center">Chỉnh Sửa: <?php echo $book['title']; ?></h3>
        <div class="card p-4 shadow-sm mt-3">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="old_image" value="<?php echo $book['image']; ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Tên sách</label>
                        <input type="text" name="title" class="form-control" value="<?php echo $book['title']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Tác giả</label>
                        <input type="text" name="author" class="form-control" value="<?php echo $book['author']; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Giá bán</label>
                        <input type="number" name="price" class="form-control" value="<?php echo $book['price']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="text-danger fw-bold">Số lượng tồn kho</label>
                        <input type="number" name="quantity" class="form-control" value="<?php echo $book['quantity']; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Nhà xuất bản</label>
                        <input type="text" name="publisher" class="form-control" value="<?php echo $book['publisher']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Năm xuất bản</label>
                        <input type="number" name="publish_year" class="form-control" value="<?php echo $book['publish_year']; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Thể loại</label>
                    <select name="category_id" class="form-select">
                        <?php
                        $cats = $conn->query("SELECT * FROM categories")->fetchAll();
                        foreach($cats as $c) {
                            $selected = ($c['id'] == $book['category_id']) ? 'selected' : '';
                            echo "<option value='".$c['id']."' $selected>".$c['name']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo $book['description']; ?></textarea>
                </div>

                <div class="mb-3">
                    <img src="../uploads/<?php echo $book['image']; ?>" width="80" class="border rounded mb-2">
                    <input type="file" name="image" class="form-control">
                </div>

                <button type="submit" class="btn btn-warning w-100">Cập Nhật Thông Tin</button>
            </form>
        </div>
    </div>
</body>
</html>