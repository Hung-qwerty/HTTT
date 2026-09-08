<?php
session_start();
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quản trị Thư viện</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="text-primary">Khu vực Quản trị (Admin)</h2>
            <a href="login.php" class="btn btn-danger">Đăng xuất</a>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm p-4 text-center">
                    <h4>Kho Sách</h4>
                    <p class="text-muted">Quản lý nhập sách, sửa xóa kho sách.</p>
                    <a href="sach.php" class="btn btn-success">Mở Kho sách</a>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm p-4 text-center">
                    <h4>Duyệt Yêu cầu Mượn</h4>
                    <p class="text-muted">Xử lý yêu cầu mượn của Sinh viên.</p>
                    <a href="phieumuon.php" class="btn btn-warning fw-bold">Duyệt Phiếu</a>
                </div>
            </div>
        </div>
    </div>
</body></html>