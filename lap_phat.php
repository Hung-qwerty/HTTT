<?php
$host = 'localhost';
$db = 'quanlythuvien';
$user = 'root';
$pass = '';
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

$ma_phieu = $_GET['ma_phieu'] ?? '';
$ma_sv = $_GET['ma_sv'] ?? '';

if (isset($_POST['xac_nhan_phat'])) {
    $ngay_lap = date('Y-m-d');
    $ly_do = $_POST['ly_do'];
    $tien_phat = $_POST['tien_phat'];

    // Đã sửa 'ma_phieu' thành 'ma_phieu_phat' cho khớp tuyệt đối với CSDL
    $stmt = $pdo->prepare("INSERT INTO bien_ban_phat (ma_phieu_phat, ma_sv, ngay_lap, ly_do, tien_phat) VALUES (?, ?, ?, ?, ?)");
    // Lấy mã phiếu (vd: PM_001) gắn luôn làm mã biên bản phạt
    $stmt->execute([$ma_phieu, $ma_sv, $ngay_lap, $ly_do, $tien_phat]);

    // Cập nhật trạng thái phiếu đã xử lý xong
    $update = $pdo->prepare("UPDATE phieumuon SET trang_thai = 'Đã trả (Đã nộp phạt)' WHERE ma_phieu = ?");
    $update->execute([$ma_phieu]);

    // Đã sửa lại đúng tên trang danh sách phiếu mượn
    header("Location: phieumuon.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lập Biên Bản Phạt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 450px;">
        <h3 class="text-danger mb-3 text-center">Lập Biên Bản Phạt</h3>
        <p class="mb-1"><strong>Mã phiếu vi phạm:</strong> <?= htmlspecialchars($ma_phieu) ?></p>
        <p class="mb-3"><strong>Mã sinh viên:</strong> <?= htmlspecialchars($ma_sv) ?></p>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Lý do xử phạt:</label>
                <input type="text" name="ly_do" class="form-control" value="Quá hạn trả sách quy định" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Số tiền phạt (VNĐ):</label>
                <input type="number" name="tien_phat" class="form-control" value="50000" required>
            </div>
            <button type="submit" name="xac_nhan_phat" class="btn btn-danger w-100 mb-2">Xác nhận thu phạt & Thu hồi sách</button>
            <a href="phieumuon.php" class="btn btn-secondary w-100">Hủy bỏ</a>
        </form>
    </div>
</body>
</html>