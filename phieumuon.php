<?php
include 'db.php';

// Xử lý tạo phiếu mượn nhanh
if (isset($_POST['tao_phieu'])) {
    $maphieu = "PM" . time(); // Tạo mã ngẫu nhiên theo thời gian
    $mabd = $_POST['ma_ban_doc'];
    $masach = $_POST['ma_sach'];
    $ngaymuon = date('Y-m-d');
    $ngayhentra = date('Y-m-d', strtotime('+7 days')); // Mặc định cho mượn 7 ngày

    try {
        // Lưu vào bảng phiếu mượn
        $stmt1 = $conn->prepare("INSERT INTO phieumuon (ma_phieu, ma_ban_doc, ngay_muon, ngay_hen_tra, trang_thai) VALUES (?, ?, ?, ?, 'Đang mượn')");
        $stmt1->execute([$maphieu, $mabd, $ngaymuon, $ngayhentra]);
        
        // Lưu vào chi tiết phiếu
        $stmt2 = $conn->prepare("INSERT INTO chitietphieu (ma_phieu, ma_sach, so_luong) VALUES (?, ?, 1)");
        $stmt2->execute([$maphieu, $masach]);
        
        header("Location: phieumuon.php");
    } catch (PDOException $e) {
        $error = "Lỗi tạo phiếu! Kiểm tra lại mã độc giả hoặc mã sách.";
    }
}

// Lấy danh sách phiếu mượn
$phieumuons = $conn->query("
    SELECT p.ma_phieu, b.ten_ban_doc, p.ngay_muon, p.ngay_hen_tra, p.trang_thai, s.ten_sach 
    FROM phieumuon p 
    JOIN bandoc b ON p.ma_ban_doc = b.ma_ban_doc
    JOIN chitietphieu c ON p.ma_phieu = c.ma_phieu
    JOIN sach s ON c.ma_sach = s.ma_sach
    ORDER BY p.ngay_muon DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phiếu Mượn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Nghiệp vụ Mượn Sách</h2>
        <a href="index.php" class="btn btn-secondary">Quay lại Menu</a>
    </div>

    <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <!-- Form tạo phiếu mượn -->
    <form method="POST" class="row g-3 mb-4 border p-3 bg-light rounded shadow-sm">
        <h5>Tạo Phiếu Mượn (Mặc định trả sau 7 ngày)</h5>
        <div class="col-md-5">
            <input type="text" name="ma_ban_doc" class="form-control" placeholder="Mã bạn đọc (VD: BD01)" required>
        </div>
        <div class="col-md-5">
            <input type="text" name="ma_sach" class="form-control" placeholder="Mã sách muốn mượn (VD: S01)" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="tao_phieu" class="btn btn-warning w-100 fw-bold">Lập Phiếu</button>
        </div>
    </form>

    <!-- Bảng hiển thị danh sách phiếu mượn -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Mã Phiếu</th>
                <th>Tên Độc Giả</th>
                <th>Tên Sách Mượn</th>
                <th>Ngày Mượn</th>
                <th>Ngày Hẹn Trả</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($phieumuons as $pm) { ?>
                <tr>
                    <td class="fw-bold text-primary"><?= htmlspecialchars($pm['ma_phieu']) ?></td>
                    <td><?= htmlspecialchars($pm['ten_ban_doc']) ?></td>
                    <td><?= htmlspecialchars($pm['ten_sach']) ?></td>
                    <td><?= htmlspecialchars($pm['ngay_muon']) ?></td>
                    <td class="text-danger fw-bold"><?= htmlspecialchars($pm['ngay_hen_tra']) ?></td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($pm['trang_thai']) ?></span></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>