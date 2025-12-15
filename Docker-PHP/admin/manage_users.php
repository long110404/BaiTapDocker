<?php
session_start();
include 'check_login.php';
include '../includes/db.php'; 

if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; 
    $fullname = $_POST['fullname']; 
    $phone = $_POST['phone'];        
    $email = $_POST['email'];
    $role = $_POST['role']; 

    try {
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = :u OR phone = :p");
        $stmt_check->execute([':u' => $username, ':p' => $phone]);
        
        if ($stmt_check->rowCount() > 0) {
            echo "<script>alert('Lỗi: Tên đăng nhập hoặc Số điện thoại đã tồn tại!');</script>";
        } else {
            $pass_hash = md5($password);

            $sql = "INSERT INTO users (username, password, fullname, phone, email, role, is_locked) 
                    VALUES (:u, :pass, :fn, :ph, :em, :r, 0)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([
                ':u' => $username,
                ':pass' => $pass_hash,
                ':fn' => $fullname,
                ':ph' => $phone,
                ':em' => $email,
                ':r' => $role
            ])) {
                echo "<script>alert('Thêm tài khoản thành công!'); window.location.href='manage_users.php';</script>";
            }
        }
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi hệ thống: " . $e->getMessage() . "');</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id == 1) { 
        echo "<script>alert('Không thể xóa Super Admin!'); window.location.href='manage_users.php';</script>";
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header("Location: manage_users.php");
        } catch (PDOException $e) {
            echo "<script>alert('Lỗi xóa người dùng (có thể do ràng buộc khóa ngoại)!'); window.location.href='manage_users.php';</script>";
        }
    }
}

$users = [];
try {
    $stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tài Khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <h3 class="text-center mb-4 fw-bold text-primary">QUẢN LÝ TÀI KHOẢN HỆ THỐNG</h3>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white fw-bold">
                        + Thêm Tài Khoản Mới
                    </div>
                    <div class="card-body">
                        <form method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label class="fw-bold">Tên đăng nhập (*)</label>
                                <input type="text" name="username" class="form-control" required placeholder="">
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold">Mật khẩu (*)</label>
                                <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu...">
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold">Họ và Tên</label>
                                <input type="text" name="fullname" class="form-control" required placeholder="">
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold">Số điện thoại (*)</label>
                                <input type="tel" name="phone" class="form-control" required placeholder="">
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="">
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-danger">Phân Quyền</label>
                                <select name="role" class="form-select">
                                    <option value="0">Khách hàng (Customer)</option>
                                    <option value="2" selected>Nhân viên (Staff)</option>
                                    <option value="1">Quản trị viên (Admin)</option>
                                </select>
                            </div>

                            <button type="submit" name="add_user" class="btn btn-primary w-100">Lưu Tài Khoản</button>
                        </form>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <a href="index.php" class="btn btn-secondary">← Quay lại Menu</a>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white fw-bold">
                        Danh Sách Người Dùng Hiện Có
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tài khoản</th>
                                        <th>Thông tin liên hệ</th>
                                        <th>Vai trò</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($users) > 0): ?>
                                        <?php foreach ($users as $row): 
                                            if ($row['role'] == 1) {
                                                $role_show = '<span class="badge bg-danger">Admin</span>';
                                            } elseif ($row['role'] == 2) {
                                                $role_show = '<span class="badge bg-primary">Nhân viên</span>';
                                            } else {
                                                $role_show = '<span class="badge bg-secondary">Khách hàng</span>';
                                            }

                                            $lock_show = ($row['is_locked'] == 1) ? '<span class="badge bg-dark ms-1">🔒 Bị khóa</span>' : '';
                                            $show_name = !empty($row['fullname']) ? $row['fullname'] : $row['username'];
                                            $show_phone = !empty($row['phone']) ? $row['phone'] : '---';
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td class="fw-bold"><?php echo $row['username'] . ' ' . $lock_show; ?></td>
                                            <td>
                                                <div class="fw-bold text-primary"><?php echo $show_name; ?></div>
                                                <div class="small"><?php echo $show_phone; ?></div>
                                                <div class="small text-muted"> <?php echo $row['email']; ?></div>
                                            </td>
                                            <td><?php echo $role_show; ?></td>
                                            <td>
                                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white">Sửa</a>
                                                <?php if ($row['id'] != 1): ?>
                                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center p-3">Chưa có người dùng nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>