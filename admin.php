<?php
session_start();
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị Thư viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="text-primary">Khu vực Quản trị (Admin Dashboard)</h2>
            <a href="login.php" class="btn btn-danger">Đăng xuất</a>
        </div>
        <div class="row g-4">
            <!-- Nút 1: Kho Sách -->
            <div class="col-md-6">
                <div class="card shadow-sm p-4 text-center h-100">
                    <h4 class="text-success">Kho Sách</h4>
                    <p class="text-muted">Quản lý nhập sách, sửa/xóa và thể loại sách.</p>
                    <a href="sach.php" class="btn btn-success mt-auto">Mở Kho sách</a>
                </div>
            </div>
            
            <!-- Nút 2: Duyệt Phiếu Mượn -->
            <div class="col-md-6">
                <div class="card shadow-sm p-4 text-center h-100">
                    <h4 class="text-warning">Quản lý Mượn/Trả</h4>
                    <p class="text-muted">Xử lý yêu cầu mượn của Sinh viên & Thu hồi sách.</p>
                    <a href="phieumuon.php" class="btn btn-warning fw-bold mt-auto">Duyệt & Quản lý Phiếu</a>
                </div>
            </div>
            
            <!-- Nút 3: Bạn Đọc -->
            <div class="col-md-6">
                <div class="card shadow-sm p-4 text-center h-100">
                    <h4 class="text-primary">Quản lý Bạn Đọc</h4>
                    <p class="text-muted">Cập nhật thông tin Sinh viên sử dụng thư viện.</p>
                    <a href="bandoc.php" class="btn btn-primary mt-auto">Mở Danh mục Bạn đọc</a>
                </div>
            </div>

            <!-- Nút 4: Thống kê Báo cáo (MỚI THÊM) -->
            <div class="col-md-6">
                <div class="card shadow-sm p-4 text-center h-100 bg-dark text-white">
                    <h4 class="text-white">Báo Cáo Thống Kê</h4>
                    <p class="text-light">Xem tổng quan số liệu kho sách, mượn trả & tiền phạt.</p>
                    <a href="thong_ke.php" class="btn btn-light fw-bold mt-auto">Xem Báo cáo ngay</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>