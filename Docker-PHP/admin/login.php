<?php
session_start();
include '../includes/db.php'; 
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_user = $_POST['username'];
    $password = $_POST['password'];
    $password_md5 = md5($password); 

    try {
        
        $sql = "SELECT * FROM users WHERE (username = :input OR phone = :input) AND password = :pass";
        $stmt = $conn->prepare($sql);
       
        $stmt->execute([
            ':input' => $input_user,
            ':pass' => $password_md5
        ]);

    
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(); 
            
            if ($user['is_locked'] == 1) {
                $error = "Tài khoản này đã bị khóa!";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 1 || $user['role'] == 2) {
                    $_SESSION['admin_user'] = $user['username'];
                    $_SESSION['admin_role'] = $user['role'];
                }

                if ($user['role'] == 1) { 
                    header("Location: index.php");
                } elseif ($user['role'] == 2) { 
                    header("Location: ../staff/index.php");
                } else { 
                    header("Location: ../index.php"); 
                }
                exit();
            }
        } else {
            $error = "Sai tên đăng nhập hoặc mật khẩu!";
        }
    } catch(PDOException $e) {
        $error = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 10px; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4 text-primary fw-bold">ĐĂNG NHẬP</h3>
        
        <?php if($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST"> 
            <div class="mb-3">
                <label class="fw-bold">Tài khoản</label>
                <input type="text" name="username" class="form-control" placeholder="Username hoặc SĐT..." required>
            </div>
            
            <div class="mb-3">
                <label class="fw-bold">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Đăng Nhập</button>
            
            <div class="text-center mt-3">
                Bạn chưa có tài khoản? <a href="../register.php" class="text-decoration-none">Đăng ký ngay</a>
            </div>
        </form>
        <div class="text-center mt-3 pt-3 border-top">
            <a href="../index.php" class="text-decoration-none text-secondary">← Về trang chủ bán hàng</a>
        </div>
    </div>
</body>
</html>