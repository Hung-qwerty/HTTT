<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }

$error = '';
$success = '';

// 1. TỰ ĐỘNG CẬP NHẬT QUÁ HẠN
$conn->exec("UPDATE phieumuon SET trang_thai = 'Quá hạn' WHERE trang_thai = 'Đang mượn' AND ngay_hen_tra < CURDATE()");

// 2. XỬ LÝ ADMIN TẠO PHIẾU MƯỢN TRỰC TIẾP
if (isset($_POST['tao_phieu'])) {
    $ma_phieu = "PM_" . time();
    $ma_sv = $_POST['ma_sv'];
    $ma_sach = $_POST['ma_sach'];
    $ngay_tao = date('Y-m-d');
    $ngay_hen_tra = $_POST['ngay_hen_tra'];

    try {
        // Kiểm tra số lượng sách trong kho
        $stmt_sach = $conn->prepare("SELECT so_luong FROM sach WHERE ma_sach = ?");
        $stmt_sach->execute([$ma_sach]);
        $sach = $stmt_sach->fetch();

        if ($sach && $sach['so_luong'] > 0) {
            // Thêm vào bảng phieumuon (Trạng thái mặc định là 'Đang mượn' khi Admin lập)
            $stmt_pm = $conn->prepare("INSERT INTO phieumuon (ma_phieu, ma_sv, ngay_tao, ngay_hen_tra, trang_thai) VALUES (?, ?, ?, ?, 'Đang mượn')");
            $stmt_pm->execute([$ma_phieu, $ma_sv, $ngay_tao, $ngay_hen_tra]);

            // Thêm vào chi tiết phiếu
            $stmt_ct = $conn->prepare("INSERT INTO chitietphieu (ma_phieu, ma_sach, so_luong) VALUES (?, ?, 1)");
            $stmt_ct->execute([$ma_phieu, $ma_sach]);

            // Trừ số lượng sách trong kho
            $conn->prepare("UPDATE sach SET so_luong = so_luong - 1 WHERE ma_sach = ?")->execute([$ma_sach]);

            $success = "Lập phiếu mượn thành công!";
        } else {
            $error = "Sách đã hết trong kho, không thể lập phiếu!";
        }
    } catch (PDOException $e) {
        $error = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
    }
}

// 3. XỬ LÝ DUYỆT YÊU CẦU MƯỢN (Từ sinh viên gửi lên)
if (isset($_GET['duyet'])) {
    $maphieu = $_GET['duyet'];
    $ngayhentra = date('Y-m-d', strtotime('+7 days')); // Mặc định hạn trả sau 7 ngày

    // Lấy mã sách trong phiếu để trừ kho
    $st = $conn->prepare("SELECT ma_sach FROM chitietphieu WHERE ma_phieu = ?");
    $st->execute([$maphieu]);
    $ct = $st->fetch();

    if ($ct) {
        $masach = $ct['ma_sach'];
        // Cập nhật trạng thái phiếu
        $conn->prepare("UPDATE phieumuon SET trang_thai = 'Đang mượn', ngay_hen_tra = ? WHERE ma_phieu = ?")->execute([$ngayhentra, $maphieu]);
        // Trừ kho sách
        $conn->prepare("UPDATE sach SET so_luong = so_luong - 1 WHERE ma_sach = ?")->execute([$masach]);
    }
    header("Location: phieumuon.php");
    exit();
}

// 4. XỬ LÝ THU HỒI SÁCH (TRẢ SÁCH)
if (isset($_GET['trasach'])) {
    $maphieu = $_GET['trasach'];

    // Lấy mã sách để cộng lại kho
    $st = $conn->prepare("SELECT ma_sach FROM chitietphieu WHERE ma_phieu = ?");
    $st->execute([$maphieu]);
    $ct = $st->fetch();

    if ($ct) {
        $masach = $ct['ma_sach'];
        // Cập nhật trạng thái phiếu thành Đã trả
        $conn->prepare("UPDATE phieumuon SET trang_thai = 'Đã trả' WHERE ma_phieu = ?")->execute([$maphieu]);
        // Cộng lại số lượng sách vào kho
        $conn->prepare("UPDATE sach SET so_luong = so_luong + 1 WHERE ma_sach = ?")->execute([$masach]);
    }
    header("Location: phieumuon.php");
    exit();
}

// Lấy danh sách phiếu mượn, danh sách sinh viên, danh sách sách để hiển thị form
$phieumuons = $conn->query("
    SELECT p.ma_phieu, b.ten_sv, b.ma_sv, p.ngay_tao, p.ngay_hen_tra, p.trang_thai, s.ten_sach, s.ma_sach 
    FROM phieumuon p 
    JOIN bandoc b ON p.ma_sv = b.ma_sv
    JOIN chitietphieu c ON p.ma_phieu = c.ma_phieu
    JOIN sach s ON c.ma_sach = s.ma_sach
    ORDER BY FIELD(p.trang_thai, 'Chờ duyệt', 'Quá hạn', 'Đang mượn', 'Đã trả'), p.ngay_tao DESC
")->fetchAll();

$danh_sach_sv = $conn->query("SELECT * FROM bandoc")->fetchAll();
$danh_sach_sach = $conn->query("SELECT * FROM sach WHERE so_luong > 0")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phiếu mượn - Trả</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold">Quản lý & Xử lý Phiếu Mượn Trả</h2>
            <a href="admin.php" class="btn btn-secondary">← Về Menu Admin</a>
        </div>

        <?php if ($error) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if ($success) { echo "<div class='alert alert-success'>$success</div>"; } ?>

        <!-- Form lập phiếu mượn trực tiếp bởi Admin -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white fw-bold">➕ Lập Phiếu Mượn</div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Chọn Sinh viên (Bạn đọc)</label>
                        <select name="ma_sv" class="form-control" required>
                            <option value="">-- Chọn sinh viên --</option>
                            <?php foreach ($danh_sach_sv as $sv) { ?>
                                <option value="<?= $sv['ma_sv'] ?>"><?= $sv['ma_sv'] ?> - <?= $sv['ten_sv'] ?> (<?= $sv['lop'] ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Chọn Sách muốn mượn</label>
                        <select name="ma_sach" class="form-control" required>
                            <option value="">-- Chọn đầu sách còn trong kho --</option>
                            <?php foreach ($danh_sach_sach as $s) { ?>
                                <option value="<?= $s['ma_sach'] ?>"><?= $s['ten_sach'] ?> (Còn: <?= $s['so_luong'] ?> quyển)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Ngày hẹn trả</label>
                        <input type="date" name="ngay_hen_tra" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" name="tao_phieu" class="btn btn-success w-100 fw-bold">Lập</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách phiếu mượn & duyệt yêu cầu -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">📋 Danh Sách Yêu Cầu & Giao Dịch Mượn Trả</div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>Mã Phiếu</th>
                            <th>Độc Giả</th>
                            <th>Sách Mượn</th>
                            <th>Ngày Gửi / Tạo</th>
                            <th>Hạn Trả</th>
                            <th>Trạng Thái</th>
                            <th class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($phieumuons) > 0) { ?>
                            <?php foreach ($phieumuons as $pm) { ?>
                                <tr>
                                    <td class="fw-bold"><?= $pm['ma_phieu'] ?></td>
                                    <td><?= htmlspecialchars($pm['ten_sv']) ?><br><small class="text-muted"><?= $pm['ma_sv'] ?></small></td>
                                    <td class="text-primary fw-semibold"><?= htmlspecialchars($pm['ten_sach']) ?></td>
                                    <td><?= $pm['ngay_tao'] ?></td>
                                    <td class="fw-bold <?= ($pm['trang_thai'] == 'Quá hạn') ? 'text-danger' : 'text-success' ?>">
                                        <?= $pm['ngay_hen_tra'] ?? '--/--/----' ?>
                                    </td>
                                    <td>
                                        <?>
                                        <?php 
                                            if ($pm['trang_thai'] == 'Chờ duyệt') echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                                            elseif ($pm['trang_thai'] == 'Đang mượn') echo '<span class="badge bg-info text-dark">Đang mượn</span>';
                                            elseif ($pm['trang_thai'] == 'Quá hạn') echo '<span class="badge bg-danger">Quá hạn (Phạt)</span>';
                                            else echo '<span class="badge bg-secondary">Đã trả</span>';
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($pm['trang_thai'] == 'Chờ duyệt') { ?>
                                            <a href="phieumuon.php?duyet=<?= $pm['ma_phieu'] ?>" class="btn btn-success btn-sm fw-bold">Duyệt mượn</a>
                                        <?php } elseif ($pm['trang_thai'] == 'Đang mượn') { ?>
                                            <a href="phieumuon.php?trasach=<?= $pm['ma_phieu'] ?>" class="btn btn-outline-danger btn-sm">Thu hồi sách</a>
                                        <?php } elseif ($pm['trang_thai'] == 'Quá hạn') { ?>
                                            <a href="lap_phat.php?ma_phieu=<?= $pm['ma_phieu'] ?>&ma_sv=<?= $pm['ma_sv'] ?>" class="btn btn-danger btn-sm fw-bold">Lập phạt & Trả</a>
                                        <?php } else { ?>
                                            <span class="text-muted small">Hoàn tất</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="7" class="text-center py-3 text-muted">Chưa có giao dịch mượn trả nào.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>