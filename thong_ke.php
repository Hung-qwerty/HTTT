<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }

// Thực hiện các câu lệnh SQL để lấy số liệu thống kê
$tong_sach = $conn->query("SELECT SUM(so_luong) FROM sach")->fetchColumn() ?: 0;
$tong_bandoc = $conn->query("SELECT COUNT(*) FROM bandoc")->fetchColumn() ?: 0;
$dang_muon = $conn->query("SELECT COUNT(*) FROM phieumuon WHERE trang_thai = 'Đang mượn'")->fetchColumn() ?: 0;
$tong_tien_phat = $conn->query("SELECT SUM(tien_phat) FROM bien_ban_phat")->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Thống Kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Báo Cáo Thống Kê Thư Viện</h2>
            <a href="admin.php" class="btn btn-secondary">← Quay lại Menu Admin</a>
        </div>

        <div class="row g-4">
            <!-- Thẻ Tổng số sách -->
            <div class="col-md-3">
                <div class="card bg-success text-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Tổng Sách Trong Kho</h5>
                        <h2 class="display-5 fw-bold"><?= htmlspecialchars($tong_sach) ?></h2>
                        <p class="card-text">Cuốn</p>
                    </div>
                </div>
            </div>
            
            <!-- Thẻ Tổng sinh viên -->
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Tổng Sinh Viên</h5>
                        <h2 class="display-5 fw-bold"><?= htmlspecialchars($tong_bandoc) ?></h2>
                        <p class="card-text">Bạn đọc đăng ký</p>
                    </div>
                </div>
            </div>

            <!-- Thẻ Sách đang mượn -->
            <div class="col-md-3">
                <div class="card bg-info text-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Phiếu Đang Mượn</h5>
                        <h2 class="display-5 fw-bold"><?= htmlspecialchars($dang_muon) ?></h2>
                        <p class="card-text">Giao dịch chưa trả</p>
                    </div>
                </div>
            </div>

            <!-- Thẻ Tổng tiền phạt -->
            <div class="col-md-3">
                <div class="card bg-danger text-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Tiền Phạt Thu Được</h5>
                        <h2 class="display-5 fw-bold"><?= number_format($tong_tien_phat, 0, ',', '.') ?></h2>
                        <p class="card-text">VNĐ</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-5 bg-white p-4 rounded shadow-sm">
            <h4 class="text-muted">Ghi chú từ hệ thống:</h4>
            <p>Báo cáo này được cập nhật theo thời gian thực (Real-time) từ cơ sở dữ liệu. Để xuất báo cáo dạng file Excel hoặc PDF, vui lòng liên hệ Ban Giám Đốc Thư Viện cài đặt thêm thư viện mở rộng.</p>
        </div>
    </div>
</body>
</html>