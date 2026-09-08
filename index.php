<?php
include 'db.php';
$tukhoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';

if (isset($_POST['gui_yeu_cau'])) {
    $masv = trim($_POST['ma_sv']);
    $masach = $_POST['ma_sach'];
    $maphieu = "YC" . time();
    $ngaytao = date('Y-m-d');
    
    $checkSV = $conn->prepare("SELECT * FROM bandoc WHERE ma_sv = ?");
    $checkSV->execute([$masv]);
    if ($checkSV->rowCount() == 0) {
        $addSV = $conn->prepare("INSERT INTO bandoc (ma_sv, ten_sv, lop) VALUES (?, ?, ?)");
        $addSV->execute([$masv, "Sinh viên mới ($masv)", "Chưa cập nhật"]);
    }

    $conn->prepare("INSERT INTO phieumuon (ma_phieu, ma_sv, ngay_tao, trang_thai) VALUES (?, ?, ?, 'Chờ duyệt')")->execute([$maphieu, $masv, $ngaytao]);
    $conn->prepare("INSERT INTO chitietphieu (ma_phieu, ma_sach, so_luong) VALUES (?, ?, 1)")->execute([$maphieu, $masach]);
    $thongbao = "Đã gửi yêu cầu mượn sách thành công! Vui lòng chờ Thủ thư duyệt.";
}

if ($tukhoa != '') {
    $stmt = $conn->prepare("SELECT s.*, t.ten_the_loai FROM sach s JOIN theloai t ON s.ma_the_loai = t.ma_the_loai WHERE s.ten_sach LIKE ? OR s.ma_sach LIKE ?");
    $stmt->execute(["%$tukhoa%", "%$tukhoa%"]);
} else {
    $stmt = $conn->query("SELECT s.*, t.ten_the_loai FROM sach s JOIN theloai t ON s.ma_the_loai = t.ma_the_loai");
}
$sachs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thư viện Trường ĐH Thủ Đô</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">📚 Thư viện Sinh viên</a>
            <a href="login.php" class="btn btn-light btn-sm">Đăng nhập Quản trị (Admin)</a>
        </div>
    </nav>
    <div class="container">
        <?php if (isset($thongbao)) echo "<div class='alert alert-success'>$thongbao</div>"; ?>
        
        <h3 class="mb-3">Tra cứu và Mượn sách</h3>
        <form method="GET" class="input-group mb-4">
            <input type="text" name="tu_khoa" class="form-control" placeholder="Nhập tên sách, mã sách..." value="<?= htmlspecialchars($tukhoa) ?>">
            <button class="btn btn-secondary" type="submit">Tìm kiếm</button>
        </form>

        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr><th>Mã Sách</th><th>Tên Sách</th><th>Tác Giả</th><th>Thể Loại</th><th>Kho</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                <?php foreach ($sachs as $s) { ?>
                    <tr>
                        <td><?= htmlspecialchars($s['ma_sach']) ?></td>
                        <td><?= htmlspecialchars($s['ten_sach']) ?></td>
                        <td><?= htmlspecialchars($s['tac_gia']) ?></td>
                        <td><?= htmlspecialchars($s['ten_the_loai']) ?></td>
                        <td><?= htmlspecialchars($s['so_luong']) ?> quyển</td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="ma_sach" value="<?= $s['ma_sach'] ?>">
                                <input type="text" name="ma_sv" class="form-control form-control-sm" placeholder="Nhập Mã SV để mượn" required>
                                <button type="submit" name="gui_yeu_cau" class="btn btn-warning btn-sm text-nowrap fw-bold">Mượn ngay</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>