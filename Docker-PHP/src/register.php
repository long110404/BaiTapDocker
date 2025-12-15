<?php
session_start();
include 'includes/db.php'; 

$error = "";
$success = "";

if (isset($_POST['register'])) {
 
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $re_password = $_POST['re_password'];
    $email = !empty($_POST['email']) ? $_POST['email'] : '';

    $name_pattern = '/^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵýỷỹ\s]+$/u';

    if (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự!";
    } elseif ($password != $re_password) {
        $error = "Mật khẩu nhập lại không khớp!";
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $error = "Số điện thoại không hợp lệ (phải là 10 hoặc 11 số)!";
    } elseif (!preg_match($name_pattern, $fullname)) {
        $error = "Họ tên không được chứa số hoặc ký tự đặc biệt!";
    } else {
        try {
            
            $sql_check = "SELECT id FROM users WHERE username = :u OR phone = :p";
            $stmt = $conn->prepare($sql_check);
            $stmt->execute([':u' => $username, ':p' => $phone]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Tên đăng nhập hoặc Số điện thoại này đã được sử dụng!";
            } else {
               
                $pass_hash = md5($password); 
                
                $sql_insert = "INSERT INTO users (username, password, fullname, phone, email, role) 
                               VALUES (:u, :p, :f, :ph, :e, 0)";
                $stmt_insert = $conn->prepare($sql_insert);
                
                if ($stmt_insert->execute([
                    ':u' => $username,
                    ':p' => $pass_hash,
                    ':f' => $fullname,
                    ':ph' => $phone,
                    ':e' => $email
                ])) {
                    $success = "Đăng ký thành công! <a href='admin/login.php' class='fw-bold'>Đăng nhập ngay</a>";
                }
            }
        } catch (PDOException $e) {
            $error = "Lỗi Database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5" style="max-width: 600px;">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white text-center py-3">
            <h4 class="mb-0 fw-bold">ĐĂNG KÝ THÀNH VIÊN</h4>
        </div>
        <div class="card-body p-4">
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required placeholder="Viết liền không dấu (vd: user123)">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="fullname" class="form-control" required placeholder="Chỉ nhập chữ (vd: Nguyễn Văn A)">
                    <small class="text-muted">Không được chứa số hoặc ký tự đặc biệt.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control" required placeholder="10 hoặc 11 số">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nhập lại mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="re_password" class="form-control" required>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" name="register" class="btn btn-primary btn-lg fw-bold">Đăng Ký Ngay</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center py-3">
            Đã có tài khoản? <a href="admin/login.php" class="text-decoration-none fw-bold">Đăng nhập ngay</a>
        </div>
    </div>
</div>

</body>
</html>