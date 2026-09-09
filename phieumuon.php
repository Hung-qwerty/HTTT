<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }

$conn->exec("UPDATE phieumuon SET trang_thai = 'Quá hạn' WHERE trang_thai = 'Đang mượn' AND ngay_hen_tra < CURDATE()");

if (isset($_GET['duyet'])) {
    $maphieu = $_GET['duyet'];
    $ngayhentra = date('Y-m-d', strtotime('+7 days'));
    $conn->prepare("UPDATE phieumuon SET trang_thai = 'Đang mượn', ngay_hen_tra = ? WHERE ma_phieu = ?")->execute([$ngayhentra, $maphieu]);
    header("Location: phieumuon.php");
    exit();
}

if (isset($_GET['trasach'])) {
    $maphieu = $_GET['trasach'];
    $conn->prepare("UPDATE phieumuon SET trang_thai = 'Đã trả' WHERE ma_phieu = ?")->execute([$maphieu]);
    header("Location: phieumuon.php");
    exit();
}

$phieumuons = $conn->query("
    SELECT p.ma_phieu, b.ten_sv, b.ma_sv, p.ngay_tao, p.ngay_hen_tra, p.trang_thai, s.ten_sach 
    FROM phieumuon p 
    JOIN bandoc b ON p.ma_sv = b.ma_sv
    JOIN chitietphieu c ON p.ma_phieu = c.ma_phieu
    JOIN sach s ON c.ma_sach = s.ma_sach
    ORDER BY FIELD(p.trang_thai, 'Quá hạn', 'Chờ duyệt', 'Đang mượn', 'Đã trả'), p.ngay_tao DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi"><head><meta charset="UTF-8"><title>Quản lý Phiếu mượn - Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container mt-4 bg-light">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Quản lý & Duyệt Yêu cầu Mượn Trả</h2>
        <a href="admin.php" class="btn btn-secondary">← Về Menu Admin</a>
    </div>

    <div class="card shadow-sm p-3 bg-white">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr><th>Mã YC</th><th>Độc giả</th><th>Sách đang mượn</th><th>Ngày gửi</th><th>Hạn trả</th><th>Trạng thái</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                <?php foreach ($phieumuons as $pm) { ?>
                    <tr>
                        <td class="fw-bold"><?= $pm['ma_phieu'] ?></td>
                        <td><?= $pm['ten_sv'] ?><br><small class="text-muted"><?= $pm['ma_sv'] ?></small></td>
                        <td class="text-primary"><?= $pm['ten_sach'] ?></td>
                        <td><?= $pm['ngay_tao'] ?></td>
                        <td class="fw-bold <?= ($pm['trang_thai'] == 'Quá hạn') ? 'text-danger' : 'text-success' ?>"><?= $pm['ngay_hen_tra'] ?? '--/--/----' ?></td>
                        <td>
                            <?php 
                                if ($pm['trang_thai'] == 'Chờ duyệt') echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                                elseif ($pm['trang_thai'] == 'Đang mượn') echo '<span class="badge bg-info text-dark">Đang mượn</span>';
                                elseif ($pm['trang_thai'] == 'Quá hạn') echo '<span class="badge bg-danger">Quá hạn (Phạt)</span>';
                                else echo '<span class="badge bg-secondary">Đã trả</span>';
                            ?>
                        </td>
                        <td>
                            <?php if ($pm['trang_thai'] == 'Chờ duyệt') { ?>
                                <a href="phieumuon.php?duyet=<?= $pm['ma_phieu'] ?>" class="btn btn-success btn-sm">Duyệt mượn</a>
                            <?php } elseif ($pm['trang_thai'] == 'Đang mượn') { ?>
                                <a href="phieumuon.php?trasach=<?= $pm['ma_phieu'] ?>" class="btn btn-outline-danger btn-sm">Thu hồi sách</a>
                            <?php } elseif ($pm['trang_thai'] == 'Quá hạn') { ?>
                                <a href="lap_phat.php?ma_phieu=<?= $pm['ma_phieu'] ?>&ma_sv=<?= $pm['ma_sv'] ?>" class="btn btn-danger btn-sm fw-bold">Lập phạt & Trả</a>
                            <?php } else { ?>
                                <button class="btn btn-light btn-sm" disabled>Hoàn tất</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body></html>