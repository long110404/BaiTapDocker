<?php
session_start();
include 'check_login.php';
include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<script>alert('Người dùng không tồn tại!'); window.location.href='manage_users.php';</script>";
        exit();
    }
} else {
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $role = intval($_POST['role']);
    $is_locked = intval($_POST['is_locked']);
    $new_pass = $_POST['new_password'];

    if ($id == 1 && ($role != 1 || $is_locked == 1)) {
        echo "<script>alert('Không thể hạ quyền hoặc khóa tài khoản Super Admin!');</script>";
    } else {
        try {
            $sql = "UPDATE users SET fullname = :fn, phone = :ph, email = :em, role = :r, is_locked = :l";
            $params = [
                ':fn' => $fullname,
                ':ph' => $phone,
                ':em' => $email,
                ':r' => $role,
                ':l' => $is_locked
            ];

            if (!empty($new_pass)) {
                $pass_hash = md5($new_pass);
                $sql .= ", password = :pass";
                $params[':pass'] = $pass_hash;
            }

            $sql .= " WHERE id = :id";
            $params[':id'] = $id;

            $stmt_update = $conn->prepare($sql);
            
            if ($stmt_update->execute($params)) {
                echo "<script>alert('Cập nhật thành công!'); window.location.href='manage_users.php';</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Lỗi Database: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Người Dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-center">Chỉnh Sửa: <?php echo htmlspecialchars($user['username']); ?></h4>
            </div>
            
            <div class="card-body p-4">
                <form method="POST">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên đăng nhập (Username)</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $user['username']; ?>" readonly>
                            <small class="text-muted">Không thể thay đổi username.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="<?php echo isset($user['phone']) ? $user['phone'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" name="fullname" class="form-control" value="<?php echo isset($user['fullname']) ? $user['fullname'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3 bg-warning-subtle p-3 rounded">
                        <label class="fw-bold text-dark">Đặt lại Mật khẩu (Reset Password)</label>
                        <input type="text" name="new_password" class="form-control border-warning" placeholder="Nhập mật khẩu mới nếu muốn đổi...">
                        <small class="text-muted">⚠ Để trống ô này nếu bạn KHÔNG muốn đổi mật khẩu.</small>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Phân Quyền (Role)</label>
                            <select name="role" class="form-select">
                                <option value="0" <?php if($user['role']==0) echo 'selected'; ?>>Khách hàng (Customer)</option>
                                <option value="2" <?php if($user['role']==2) echo 'selected'; ?>>Nhân viên (Staff)</option>
                                <option value="1" <?php if($user['role']==1) echo 'selected'; ?>>Quản trị viên (Admin)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold text-danger">Trạng thái hoạt động</label>
                            <select name="is_locked" class="form-select border-danger">
                                <option value="0" <?php if($user['is_locked']==0) echo 'selected'; ?>>Hoạt động bình thường</option>
                                <option value="1" <?php if($user['is_locked']==1) echo 'selected'; ?>> ĐÃ KHÓA (Cấm truy cập)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary flex-fill">Lưu Thay Đổi</button>
                        <a href="manage_users.php" class="btn btn-secondary flex-fill">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>