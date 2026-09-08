<?php
include 'db.php';
session_start(); if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }
// Xử lý thêm sách
if (isset($_POST['them_sach'])) {
    $ma = $_POST['ma_sach'];
    $ten = $_POST['ten_sach'];
    $tg = $_POST['tac_gia'];
    $sl = $_POST['so_luong'];
    $tl = $_POST['ma_the_loai'];
    
    try {
        $stmt = $conn->prepare("INSERT INTO sach (ma_sach, ten_sach, tac_gia, so_luong, ma_the_loai) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$ma, $ten, $tg, $sl, $tl]);
        header("Location: sach.php");
    } catch (PDOException $e) {
        $error = "Lỗi: Mã sách đã tồn tại!";
    }
}

$sachs = $conn->query("SELECT sach.*, theloai.ten_the_loai FROM sach LEFT JOIN theloai ON sach.ma_the_loai = theloai.ma_the_loai")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Danh mục Sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Danh mục Sách</h2>
        <a href="index.php" class="btn btn-secondary">Quay lại Menu</a>
    </div>

    <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <!-- Form thêm sách -->
    <form method="POST" class="row g-3 mb-4 border p-3 bg-light rounded">
        <h5>Thêm sách mới</h5>
        <div class="col-md-2"><input type="text" name="ma_sach" class="form-control" placeholder="Mã sách (VD: S04)" required></div>
        <div class="col-md-3"><input type="text" name="ten_sach" class="form-control" placeholder="Tên sách" required></div>
        <div class="col-md-2"><input type="text" name="tac_gia" class="form-control" placeholder="Tác giả"></div>
        <div class="col-md-2"><input type="number" name="so_luong" class="form-control" placeholder="Số lượng" required></div>
        <div class="col-md-2">
            <select name="ma_the_loai" class="form-control">
                <option value="1">Công nghệ thông tin</option>
                <option value="2">Kinh tế</option>
            </select>
        </div>
        <div class="col-md-1"><button type="submit" name="them_sach" class="btn btn-success w-100">Thêm</button></div>
    </form>

    <!-- Bảng hiển thị danh sách -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Mã Sách</th>
                <th>Tên Sách</th>
                <th>Tác Giả</th>
                <th>Số Lượng</th>
                <th>Thể Loại</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sachs as $s) { ?>
                <tr>
                    <td><?= htmlspecialchars($s['ma_sach']) ?></td>
                    <td><?= htmlspecialchars($s['ten_sach']) ?></td>
                    <td><?= htmlspecialchars($s['tac_gia']) ?></td>
                    <td><?= htmlspecialchars($s['so_luong']) ?></td>
                    <td><?= htmlspecialchars($s['ten_the_loai']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>