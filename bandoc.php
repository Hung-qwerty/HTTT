<?php
$host = 'localhost';
$db = 'quanlythuvien';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Xử lý thêm sinh viên mới
if (isset($_POST['them_sv'])) {
    $ma_sv = trim($_POST['ma_sv']);
    $ten_sv = trim($_POST['ten_sv']);
    $lop = trim($_POST['lop']);

    if (!empty($ma_sv) && !empty($ten_sv) && !empty($lop)) {
        // Đã đổi SINH_VIEN thành bandoc
        $stmt = $pdo->prepare("INSERT INTO bandoc (ma_sv, ten_sv, lop) VALUES (?, ?, ?)");
        $stmt->execute([$ma_sv, $ten_sv, $lop]);
        header("Location: bandoc.php");
        exit();
    }
}

// Đã đổi SINH_VIEN thành bandoc
$sinhvien = $pdo->query("SELECT * FROM bandoc")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Bạn đọc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý Danh mục Bạn đọc (Sinh viên)</h2>
            <a href="admin.php" class="btn btn-secondary">← Quay lại Menu Admin</a>
        </div>
        
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Thêm bạn đọc mới</h5>
                <form method="POST" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="ma_sv" class="form-control" placeholder="Mã SV (VD: SV01)" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="ten_sv" class="form-control" placeholder="Họ và tên sinh viên" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="lop" class="form-control" placeholder="Lớp (VD: 30INF048)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="them_sv" class="btn btn-success w-100">Thêm mới</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Danh sách sinh viên thư viện</h5>
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã Sinh Viên</th>
                            <th>Tên Sinh Viên</th>
                            <th>Lớp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sinhvien) > 0): ?>
                            <?php foreach ($sinhvien as $sv): ?>
                            <tr>
                                <td><?= htmlspecialchars($sv['ma_sv']) ?></td>
                                <td><?= htmlspecialchars($sv['ten_sv']) ?></td>
                                <td><?= htmlspecialchars($sv['lop']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted">Chưa có dữ liệu sinh viên nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>