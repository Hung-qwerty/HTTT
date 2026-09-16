<?php
session_start();
include 'db.php';
if (!isset($_SESSION['admin_logged'])) { header("Location: login.php"); exit; }

// 4 Chỉ số cơ bản
$tong_sach = $conn->query("SELECT SUM(so_luong) FROM sach")->fetchColumn() ?: 0;
$tong_bandoc = $conn->query("SELECT COUNT(*) FROM bandoc")->fetchColumn() ?: 0;
$dang_muon = $conn->query("SELECT COUNT(*) FROM phieumuon WHERE trang_thai = 'Đang mượn'")->fetchColumn() ?: 0;
$tong_tien_phat = $conn->query("SELECT SUM(tien_phat) FROM bien_ban_phat")->fetchColumn() ?: 0;

// Dữ liệu cho Biểu đồ (Tỷ lệ sách theo thể loại)
$chart_data = $conn->query("SELECT t.ten_the_loai, IFNULL(SUM(s.so_luong), 0) as so_luong FROM theloai t LEFT JOIN sach s ON t.ma_the_loai = s.ma_the_loai GROUP BY t.ma_the_loai")->fetchAll();

// Dữ liệu Top sách mượn nhiều nhất
$top_sach = $conn->query("SELECT s.ten_sach, COUNT(c.ma_sach) as luot_muon FROM chitietphieu c JOIN sach s ON c.ma_sach = s.ma_sach GROUP BY c.ma_sach ORDER BY luot_muon DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo Cáo Thống Kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Thể loại', 'Số lượng sách'],
          <?php foreach($chart_data as $cd) { echo "['".$cd['ten_the_loai']."', ".$cd['so_luong']."],"; } ?>
        ]);
        var options = { title: 'Tỷ lệ sách theo thể loại', pieHole: 0.4, colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1'] };
        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
      }
    </script>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold">Báo Cáo Tổng Quan</h2>
            <a href="admin.php" class="btn btn-secondary">← Quay lại Admin</a>
        </div>

        <!-- 4 Thẻ chỉ số -->
        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="card bg-success text-white shadow-sm h-100 p-3 text-center"><h5>Tổng Sách Kho</h5><h2><?= $tong_sach ?></h2></div></div>
            <div class="col-md-3"><div class="card bg-primary text-white shadow-sm h-100 p-3 text-center"><h5>Sinh Viên</h5><h2><?= $tong_bandoc ?></h2></div></div>
            <div class="col-md-3"><div class="card bg-info text-white shadow-sm h-100 p-3 text-center"><h5>Đang Mượn</h5><h2><?= $dang_muon ?></h2></div></div>
            <div class="col-md-3"><div class="card bg-danger text-white shadow-sm h-100 p-3 text-center"><h5>Tiền Phạt (VNĐ)</h5><h2><?= number_format($tong_tien_phat, 0, ',', '.') ?></h2></div></div>
        </div>

        <!-- Biểu đồ và Top sách -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100 p-3">
                    <div id="donutchart" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white fw-bold">🔥 Top 5 Sách Mượn Nhiều Nhất</div>
                    <ul class="list-group list-group-flush">
                        <?php foreach($top_sach as $top) { ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($top['ten_sach']) ?>
                                <span class="badge bg-primary rounded-pill"><?= $top['luot_muon'] ?> lượt</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>