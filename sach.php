<?php
include 'db.php';
session_start(); 
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }

$error = '';
$success = '';
$tukhoa = isset($_GET['tu_khoa']) ? trim($_GET['tu_khoa']) : '';

// 1. XỬ LÝ XÓA SÁCH
if (isset($_GET['xoa'])) {
    $ma_sach_xoa = $_GET['xoa'];
    try {
        $stmt = $conn->prepare("DELETE FROM sach WHERE ma_sach = ?");
        $stmt->execute([$ma_sach_xoa]);
        header("Location: sach.php");
        exit();
    } catch (PDOException $e) {
        $error = "Không thể xóa! Sách này đang nằm trong lịch sử mượn/trả của thư viện.";
    }
}

// 2. XỬ LÝ THÊM HOẶC CẬP NHẬT SÁCH
if (isset($_POST['luu_sach'])) {
    $ma = $_POST['ma_sach'];
    $ten = $_POST['ten_sach'];
    $tg = $_POST['tac_gia'];
    $sl = $_POST['so_luong'];
    $tl = $_POST['ma_the_loai'];
    $is_update = $_POST['is_update'];

    try {
        if ($is_update == '1') {
            $stmt = $conn->prepare("UPDATE sach SET ten_sach=?, tac_gia=?, so_luong=?, ma_the_loai=? WHERE ma_sach=?");
            $stmt->execute([$ten, $tg, $sl, $tl, $ma]);
            $success = "Cập nhật sách thành công!";
        } else {
            $stmt = $conn->prepare("INSERT INTO sach (ma_sach, ten_sach, tac_gia, so_luong, ma_the_loai) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ma, $ten, $tg, $sl, $tl]);
            $success = "Thêm sách mới thành công!";
        }
    } catch (PDOException $e) {
        $error = "Lỗi: Mã sách đã tồn tại hoặc có lỗi cơ sở dữ liệu!";
    }
}

// 3. LẤY DỮ LIỆU SỬA
$sach_sua = null;
if (isset($_GET['sua'])) {
    $stmt = $conn->prepare("SELECT * FROM sach WHERE ma_sach = ?");
    $stmt->execute([$_GET['sua']]);
    $sach_sua = $stmt->fetch();
}

// 4. TRUY VẤN DANH SÁCH (HỖ TRỢ TÌM KIẾM)
if ($tukhoa != '') {
    $stmt = $conn->prepare("SELECT sach.*, theloai.ten_the_loai FROM sach LEFT JOIN theloai ON sach.ma_the_loai = theloai.ma_the_loai WHERE sach.ma_sach LIKE ? OR sach.ten_sach LIKE ? OR sach.tac_gia LIKE ? ORDER BY sach.ma_sach DESC");
    $like = "%$tukhoa%";
    $stmt->execute([$like, $like, $like]);
    $sachs = $stmt->fetchAll();
} else {
    $sachs = $conn->query("SELECT sach.*, theloai.ten_the_loai FROM sach LEFT JOIN theloai ON sach.ma_the_loai = theloai.ma_the_loai ORDER BY sach.ma_sach DESC")->fetchAll();
}
$theloais = $conn->query("SELECT * FROM theloai")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Kho Sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold">Quản lý Kho Sách & Thao Tác</h2>
            <a href="admin.php" class="btn btn-secondary">← Quay lại Admin</a>
        </div>

        <?php if ($error) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if ($success) { echo "<div class='alert alert-success'>$success</div>"; } ?>

        <!-- Thanh tìm kiếm -->
        <form method="GET" class="input-group mb-4 shadow-sm">
            <input type="text" name="tu_khoa" class="form-control" placeholder="🔍 Tìm kiếm theo mã sách, tên sách, tác giả..." value="<?= htmlspecialchars($tukhoa) ?>">
            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            <?php if($tukhoa != '') { ?>
                <a href="sach.php" class="btn btn-outline-secondary">Làm mới</a>
            <?php } ?>
        </form>

        <!-- Form Thêm / Sửa -->
        <form method="POST" class="row g-3 mb-4 border p-3 bg-white rounded shadow-sm align-items-end">
            <h5 class="text-primary"><?= $sach_sua ? '✏️ Cập nhật thông tin sách' : '➕ Thêm sách mới vào kho' ?></h5>
            <input type="hidden" name="is_update" value="<?= $sach_sua ? '1' : '0' ?>">

            <div class="col-md-2">
                <label class="form-label small text-muted">Mã sách</label>
                <input type="text" name="ma_sach" class="form-control" value="<?= $sach_sua['ma_sach'] ?? '' ?>" <?= $sach_sua ? 'readonly' : '' ?> placeholder="VD: S05" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Tên sách</label>
                <input type="text" name="ten_sach" class="form-control" value="<?= $sach_sua['ten_sach'] ?? '' ?>" placeholder="Nhập tên sách" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Tác giả</label>
                <input type="text" name="tac_gia" class="form-control" value="<?= $sach_sua['tac_gia'] ?? '' ?>" placeholder="Nhập tác giả">
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted">Số lượng</label>
                <input type="number" name="so_luong" class="form-control" value="<?= $sach_sua['so_luong'] ?? '' ?>" placeholder="SL" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Thể loại</label>
                <select name="ma_the_loai" class="form-control" required>
                    <option value="">-- Chọn thể loại --</option>
                    <?php foreach ($theloais as $tl) { ?>
                        <option value="<?= $tl['ma_the_loai'] ?>" <?= ($sach_sua && $sach_sua['ma_the_loai'] == $tl['ma_the_loai']) ? 'selected' : '' ?>>
                            <?= $tl['ten_the_loai'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" name="luu_sach" class="btn <?= $sach_sua ? 'btn-warning' : 'btn-success' ?> w-100 fw-bold">
                    <?= $sach_sua ? 'Lưu' : 'Thêm' ?>
                </button>
                <?php if ($sach_sua) { ?>
                    <a href="sach.php" class="btn btn-secondary">Hủy</a>
                <?php } ?>
            </div>
        </form>

        <!-- Bảng hiển thị -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Mã</th>
                            <th>Tên Sách</th>
                            <th>Tác Giả</th>
                            <th class="text-center">Số Lượng</th>
                            <th>Thể Loại</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($sachs) > 0) { ?>
                            <?php foreach ($sachs as $s) { ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= htmlspecialchars($s['ma_sach']) ?></td>
                                    <td class="text-primary fw-semibold"><?= htmlspecialchars($s['ten_sach']) ?></td>
                                    <td><?= htmlspecialchars($s['tac_gia']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($s['so_luong']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($s['ten_the_loai']) ?></span></td>
                                    <td class="text-center">
                                        <a href="sach.php?sua=<?= $s['ma_sach'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                        <a href="sach.php?xoa=<?= $s['ma_sach'] ?>" onclick="return confirm('Xóa cuốn sách này?')" class="btn btn-sm btn-outline-danger">Xóa</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Không tìm thấy cuốn sách phù hợp.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>