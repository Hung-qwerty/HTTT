<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }

if (isset($_POST['them_tl'])) {
    $ten = trim($_POST['ten_the_loai']);
    if (!empty($ten)) {
        $conn->prepare("INSERT INTO theloai (ten_the_loai) VALUES (?)")->execute([$ten]);
        header("Location: the_loai.php"); exit();
    }
}
if (isset($_GET['xoa'])) {
    $conn->prepare("DELETE FROM theloai WHERE ma_the_loai = ?")->execute([$_GET['xoa']]);
    header("Location: the_loai.php"); exit();
}

$theloais = $conn->query("SELECT t.*, COUNT(s.ma_sach) as so_sach FROM theloai t LEFT JOIN sach s ON t.ma_the_loai = s.ma_the_loai GROUP BY t.ma_the_loai")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thể Loại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between mb-4">
            <h2>Quản lý Danh mục Thể loại</h2>
            <a href="admin.php" class="btn btn-secondary">← Menu Admin</a>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>Thêm Thể loại mới</h5>
                    <form method="POST">
                        <input type="text" name="ten_the_loai" class="form-control mb-2" placeholder="Tên thể loại (VD: Khoa học)" required>
                        <button type="submit" name="them_tl" class="btn btn-success w-100">Thêm</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm p-3">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark"><tr><th>Mã</th><th>Tên Thể loại</th><th>Đầu sách hiện có</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            <?php foreach ($theloais as $tl) { ?>
                            <tr>
                                <td><?= $tl['ma_the_loai'] ?></td>
                                <td><?= htmlspecialchars($tl['ten_the_loai']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= $tl['so_sach'] ?> cuốn</span></td>
                                <td><a href="the_loai.php?xoa=<?= $tl['ma_the_loai'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa thể loại này?')">Xóa</a></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>