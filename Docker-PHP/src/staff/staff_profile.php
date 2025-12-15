<?php
include 'check_staff.php';
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    
    try {
        $stmt = $conn->prepare("UPDATE users SET fullname = :fn WHERE id = :id");
        $stmt->execute([':fn' => $fullname, ':id' => $user_id]);
        
        $_SESSION['fullname'] = $fullname;
        
        echo "<script>alert('Đã cập nhật tên hiển thị!'); window.location.href='staff_profile.php';</script>";
    } catch(PDOException $e) {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    die("Lỗi hệ thống");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Nhân Viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow p-4">
            <h3 class="text-center mb-4 text-primary">Hồ Sơ Của Tôi</h3>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="fw-bold">Họ và Tên</label>
                    <input type="text" name="fullname" class="form-control" value="<?php echo isset($user['fullname']) ? $user['fullname'] : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold">Tài khoản (Username)</label>
                    <input type="text" class="form-control bg-secondary-subtle" value="<?php echo $user['username']; ?>" readonly>
                </div>

                <div class="alert alert-info">
                    <strong>Lịch Làm Việc:</strong><br>
                    <?php echo (isset($user['work_schedule']) && !empty($user['work_schedule'])) ? nl2br($user['work_schedule']) : "Chưa có lịch phân công."; ?>
                </div>
                
                <div class="alert alert-warning small">
                    Mật khẩu và quyền hạn do Quản lý (Admin) cấp. Nếu cần thay đổi, vui lòng liên hệ cấp trên.
                </div>

                <button type="submit" class="btn btn-primary w-100">Cập Nhật Họ Tên</button>
                <a href="index.php" class="btn btn-secondary w-100 mt-2">← Quay lại Bàn làm việc</a>
            </form>
        </div>
    </div>
</body>
</html>