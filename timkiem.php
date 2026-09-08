<?php
include 'db.php';
$tukhoa = isset($_GET['tu_khoa']) ? $_GET['tu_khoa'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tra cứu sách - Quản lý thư viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>Tra cứu thông tin sách</h2>
    <form method="GET" class="input-group mb-3">
        <input type="text" name="tu_khoa" class="form-control" placeholder="Nhập mã sách, tên sách hoặc tác giả..." value="<?= htmlspecialchars($tukhoa) ?>">
        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Mã Sách</th>
                <th>Tên Sách</th>
                <th>Tác Giả</th>
                <th>Số Lượng</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($tukhoa != '') {
                $stmt = $conn->prepare("SELECT * FROM sach WHERE ma_sach LIKE ? OR ten_sach LIKE ? OR tac_gia LIKE ?");
                $searchWildcard = "%$tukhoa%";
                $stmt->execute([$searchWildcard, $searchWildcard, $searchWildcard]);
                $ketqua = $stmt->fetchAll();

                foreach ($ketqua as $row) {
                    echo "<tr>
                            <td>{$row['ma_sach']}</td>
                            <td>{$row['ten_sach']}</td>
                            <td>{$row['tac_gia']}</td>
                            <td>{$row['so_luong']}</td>
                          </tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <a href="index.php" class="btn btn-secondary">Quay lại Menu</a>
</body>
</html>