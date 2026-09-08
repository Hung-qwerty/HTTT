<?php
session_start();
include 'db.php';

if (isset($_POST['dangnhap'])) {
    $tk = trim($_POST['taikhoan']);
    $mk = trim($_POST['matkhau']);
    
    $stmt = $conn->prepare("SELECT * FROM admin WHERE taikhoan = ? AND matkhau = ?");
    $stmt->execute([$tk, $mk]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['admin_logged'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi"><head><meta charset="UTF-8"><title>Đăng nhập Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <form method="POST" class="card p-4 shadow-sm" style="width: 350px;">
        <h4 class="text-center text-primary mb-4">Admin Thư Viện</h4>
        <?php if (isset($error)) echo "<div class='alert alert-danger p-2'>$error</div>"; ?>
        <input type="text" name="taikhoan" class="form-control mb-3" placeholder="Tài khoản" required>
        <input type="password" name="matkhau" class="form-control mb-3" placeholder="Mật khẩu" required>
        <button type="submit" name="dangnhap" class="btn btn-primary w-100">Đăng Nhập</button>
        <a href="index.php" class="text-center d-block mt-3 text-decoration-none">← Quay lại trang sinh viên</a>
    </form>
</body></html>