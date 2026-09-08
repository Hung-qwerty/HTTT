<?php
$host = 'localhost';
$username = 'root';
$password = '';

// Thiết kế giao diện báo cáo cài đặt
echo "<!DOCTYPE html><html lang='vi'><head><meta charset='UTF-8'><title>Cài đặt Hệ thống</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head>";
echo "<body class='container mt-5'><div class='card shadow-sm p-4'><h2 class='text-primary mb-4'>Tiến trình cài đặt Database</h2>";

try {
    // 1. Kết nối MySQL (chưa chọn DB)
    $conn = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Tạo DB nếu chưa có
    $conn->exec("CREATE DATABASE IF NOT EXISTS quanlythuvien CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "<p class='text-success'>✅ Đã tạo hoặc kiểm tra CSDL <b>quanlythuvien</b> thành công.</p>";

    // 3. Chọn DB để thao tác
    $conn->exec("USE quanlythuvien");

    // 4. Tạo các bảng
    $tables = [
        "CREATE TABLE IF NOT EXISTS theloai (ma_the_loai INT AUTO_INCREMENT PRIMARY KEY, ten_the_loai VARCHAR(100) NOT NULL)",
        "CREATE TABLE IF NOT EXISTS sach (ma_sach VARCHAR(50) PRIMARY KEY, ten_sach VARCHAR(255) NOT NULL, tac_gia VARCHAR(100), so_luong INT, ma_the_loai INT, FOREIGN KEY (ma_the_loai) REFERENCES theloai(ma_the_loai))",
        "CREATE TABLE IF NOT EXISTS bandoc (ma_ban_doc VARCHAR(50) PRIMARY KEY, ten_ban_doc VARCHAR(100) NOT NULL, sdt VARCHAR(15), dia_chi VARCHAR(255))",
        "CREATE TABLE IF NOT EXISTS phieumuon (ma_phieu VARCHAR(50) PRIMARY KEY, ma_ban_doc VARCHAR(50), ngay_muon DATE, ngay_hen_tra DATE, ngay_tra_thuc_te DATE, trang_thai VARCHAR(50), FOREIGN KEY (ma_ban_doc) REFERENCES bandoc(ma_ban_doc))",
        "CREATE TABLE IF NOT EXISTS chitietphieu (ma_phieu VARCHAR(50), ma_sach VARCHAR(50), so_luong INT, PRIMARY KEY (ma_phieu, ma_sach), FOREIGN KEY (ma_phieu) REFERENCES phieumuon(ma_phieu), FOREIGN KEY (ma_sach) REFERENCES sach(ma_sach))"
    ];
    foreach ($tables as $sql) {
        $conn->exec($sql);
    }
    echo "<p class='text-success'>✅ Đã tạo cấu trúc các bảng (Sách, Bạn đọc, Phiếu mượn...) thành công.</p>";

    // 5. Thêm dữ liệu mẫu nếu bảng theloai đang trống
    $check = $conn->query("SELECT COUNT(*) FROM theloai")->fetchColumn();
    if ($check == 0) {
        $conn->exec("INSERT INTO theloai (ten_the_loai) VALUES ('Công nghệ thông tin'), ('Kinh tế'), ('Văn học')");
        $conn->exec("INSERT INTO sach (ma_sach, ten_sach, tac_gia, so_luong, ma_the_loai) VALUES 
            ('S01', 'Lập trình Web với PHP', 'Nguyễn Văn A', 10, 1),
            ('S02', 'Phân tích thiết kế hệ thống', 'Trần Văn B', 5, 1),
            ('S03', 'Kinh tế vi mô', 'Lê Thị C', 8, 2)");
        $conn->exec("INSERT INTO bandoc (ma_ban_doc, ten_ban_doc, sdt, dia_chi) VALUES 
            ('BD01', 'Trần Hùng (CNTT D2021)', '0123456789', 'ĐH Thủ đô Hà Nội'),
            ('BD02', 'Nguyễn Thị Lan', '0987654321', 'Hà Nội')");
        // Giả lập 1 phiếu mượn đang diễn ra
        $conn->exec("INSERT INTO phieumuon (ma_phieu, ma_ban_doc, ngay_muon, ngay_hen_tra, trang_thai) VALUES ('PM01', 'BD01', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Đang mượn')");
        $conn->exec("INSERT INTO chitietphieu (ma_phieu, ma_sach, so_luong) VALUES ('PM01', 'S02', 1)");

        echo "<p class='text-success'>✅ Đã nạp dữ liệu mẫu thành công.</p>";
    } else {
        echo "<p class='text-secondary'>ℹ️ Dữ liệu mẫu đã tồn tại, bỏ qua bước nạp dữ liệu.</p>";
    }

    echo "<hr><h4 class='text-primary mt-3'>🎉 Cài đặt hoàn tất 100%!</h4>";
    echo "<a href='index.php' class='btn btn-primary btn-lg mt-2'>Đi đến Trang chủ Hệ thống</a>";

} catch (PDOException $e) {
    echo "<p class='text-danger'>❌ Lỗi cài đặt: " . $e->getMessage() . "</p>";
}
echo "</div></body></html>";
?>