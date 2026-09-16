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
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="text-primary fw-bold">Bảng Điều Khiển</h2>
            <a href="login.php" class="btn btn-outline-danger">Đăng xuất</a>
        </div>
        
        <div class="row g-4">
            <!-- Kho sách -->
            <div class="col-md-4"><div class="card shadow-sm p-4 text-center h-100"><h4 class="text-success">Kho Sách</h4><p class="text-muted small">Nhập/Sửa/Xóa sách trong kho.</p><a href="sach.php" class="btn btn-success mt-auto">Mở Kho sách</a></div></div>
            
            <!-- Thể loại (MỚI) -->
            <div class="col-md-4"><div class="card shadow-sm p-4 text-center h-100"><h4 class="text-info">Thể Loại Sách</h4><p class="text-muted small">Quản lý các danh mục phân loại sách.</p><a href="the_loai.php" class="btn btn-info text-white mt-auto">Quản lý Thể loại</a></div></div>
            
            <!-- Bạn đọc -->
            <div class="col-md-4"><div class="card shadow-sm p-4 text-center h-100"><h4 class="text-primary">Bạn Đọc</h4><p class="text-muted small">Cập nhật hồ sơ Sinh viên.</p><a href="bandoc.php" class="btn btn-primary mt-auto">Mở Bạn đọc</a></div></div>

            <!-- Mượn Trả -->
            <div class="col-md-6"><div class="card shadow-sm p-4 text-center h-100 border-warning border-2"><h4 class="text-warning">Quản lý Mượn / Trả</h4><p class="text-muted small">Duyệt phiếu mượn online, thu hồi sách và lập phạt quá hạn.</p><a href="phieumuon.php" class="btn btn-warning fw-bold mt-auto">Xử lý Mượn/Trả</a></div></div>
            
            <!-- Thống kê (NÂNG CẤP) -->
            <div class="col-md-6"><div class="card shadow-sm p-4 text-center h-100 bg-dark text-white"><h4 class="text-white">Báo Cáo Thống Kê</h4><p class="text-light small">Xem biểu đồ trực quan, top sách mượn nhiều và tiền phạt.</p><a href="thong_ke.php" class="btn btn-light fw-bold mt-auto">Xem Báo cáo ngay</a></div></div>
        </div>
    </div>
</body>
</html>