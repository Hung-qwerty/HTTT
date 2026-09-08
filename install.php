<?php
$host = 'localhost'; $username = 'root'; $password = '';
echo "<!DOCTYPE html><html lang='vi'><head><meta charset='UTF-8'><title>Cài đặt Hệ thống</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='bg-light d-flex justify-content-center align-items-center vh-100'><div class='card shadow-lg p-5' style='max-width: 600px; width: 100%;'><h2 class='text-primary fw-bold text-center mb-4'>Tiến trình Cài đặt Database V3</h2>";

try {
    $conn = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("DROP DATABASE IF EXISTS quanlythuvien");
    $conn->exec("CREATE DATABASE quanlythuvien CHARACTER SET utf8 COLLATE utf8_general_ci");
    $conn->exec("USE quanlythuvien");

    // Tạo bảng
    $tables = [
        "CREATE TABLE admin (taikhoan VARCHAR(50) PRIMARY KEY, matkhau VARCHAR(255))",
        "CREATE TABLE theloai (ma_the_loai INT AUTO_INCREMENT PRIMARY KEY, ten_the_loai VARCHAR(100) NOT NULL)",
        "CREATE TABLE sach (ma_sach VARCHAR(50) PRIMARY KEY, ten_sach VARCHAR(255) NOT NULL, tac_gia VARCHAR(100), so_luong INT, ma_the_loai INT, FOREIGN KEY (ma_the_loai) REFERENCES theloai(ma_the_loai))",
        "CREATE TABLE bandoc (ma_sv VARCHAR(50) PRIMARY KEY, ten_sv VARCHAR(100) NOT NULL, lop VARCHAR(50))",
        "CREATE TABLE phieumuon (ma_phieu VARCHAR(50) PRIMARY KEY, ma_sv VARCHAR(50), ngay_tao DATE, ngay_hen_tra DATE, trang_thai VARCHAR(50), FOREIGN KEY (ma_sv) REFERENCES bandoc(ma_sv))",
        "CREATE TABLE chitietphieu (ma_phieu VARCHAR(50), ma_sach VARCHAR(50), so_luong INT, PRIMARY KEY (ma_phieu, ma_sach), FOREIGN KEY (ma_phieu) REFERENCES phieumuon(ma_phieu), FOREIGN KEY (ma_sach) REFERENCES sach(ma_sach))"
    ];
    foreach ($tables as $sql) $conn->exec($sql);

    // Dữ liệu tài khoản & Thể loại
    $conn->exec("INSERT INTO admin (taikhoan, matkhau) VALUES ('admin', '123')");
    $conn->exec("INSERT INTO theloai (ten_the_loai) VALUES ('Công nghệ thông tin'), ('Luật & Kinh tế'), ('Kỹ thuật & Điện tử')");
    
    // Thêm 14 sách đa dạng chuyên ngành
    $conn->exec("INSERT INTO sach (ma_sach, ten_sach, tac_gia, so_luong, ma_the_loai) VALUES 
        ('S01', 'Giáo trình Cấu trúc dữ liệu và Giải thuật', 'Nguyễn Văn A', 15, 1),
        ('S02', 'Lập trình Web PHP & MySQL', 'Trần Hữu B', 10, 1),
        ('S03', 'Lập trình Vi điều khiển ESP32 và ESP8266', 'NXB Khoa học', 12, 3),
        ('S04', 'Thiết kế mạng LAN với Cisco Packet Tracer', 'Trần Mạng', 8, 1),
        ('S05', 'Cơ bản về VLAN, DHCP và Static Routing', 'NXB Bách Khoa', 15, 1),
        ('S06', 'Lập trình Java - Xây dựng ứng dụng quản lý', 'Lê Code', 10, 1),
        ('S07', 'Thiết kế UI/UX thực chiến với Figma', 'Phạm UX', 20, 1),
        ('S08', 'Hệ thống tự động hóa: Băng chuyền phân loại', 'NXB Kỹ Thuật', 7, 3),
        ('S09', 'Tự học Arduino từ cơ bản đến nâng cao', 'Vũ Arduino', 14, 3),
        ('S10', 'Luật Tố tụng Hành chính (TTHC) 2015', 'NXB Chính trị', 25, 2),
        ('S11', 'Phát triển Robot AI thông minh', 'NXB Công Nghệ', 6, 3),
        ('S12', 'Kinh tế Vĩ mô cơ bản', 'Lê Thị C', 20, 2),
        ('S13', 'Quản trị Cơ sở dữ liệu SQL Server', 'Hoàng SQL', 18, 1),
        ('S14', 'Linh kiện điện tử và Mạch điện', 'Nguyễn Mạch', 10, 3)");

    // Thêm 3 Sinh viên
    $conn->exec("INSERT INTO bandoc (ma_sv, ten_sv, lop) VALUES 
        ('SV01', 'Trần Hùng', 'CNTT D2021A'),
        ('SV02', 'Nguyễn Hà Giang', 'CNTT D2021A'),
        ('SV03', 'Hà Minh Quang', 'CNTT D2021A')");

    // Thêm 3 Dữ liệu Phiếu mượn mẫu: 
    // SV01: Đang mượn (Còn hạn đến 7 ngày sau)
    // SV02: Quá hạn (Đã hết hạn từ 5 ngày trước)
    // SV03: Chờ duyệt (Vừa yêu cầu hôm nay)
    $today = date('Y-m-d');
    $future_date = date('Y-m-d', strtotime('+7 days'));
    $past_date = date('Y-m-d', strtotime('-5 days'));
    
    $conn->exec("INSERT INTO phieumuon (ma_phieu, ma_sv, ngay_tao, ngay_hen_tra, trang_thai) VALUES 
        ('PM_001', 'SV01', '$today', '$future_date', 'Đang mượn'),
        ('PM_002', 'SV02', '2026-08-20', '$past_date', 'Quá hạn'),
        ('YC_003', 'SV03', '$today', NULL, 'Chờ duyệt')");

    $conn->exec("INSERT INTO chitietphieu (ma_phieu, ma_sach, so_luong) VALUES 
        ('PM_001', 'S03', 1),
        ('PM_002', 'S10', 1),
        ('YC_003', 'S04', 1)");

    echo "<div class='alert alert-success'>✅ Đã tạo cấu trúc, 14 sách và 3 luồng dữ liệu mượn/trả mẫu thành công!</div>";
    echo "<div class='d-grid gap-2 mt-4'><a href='index.php' class='btn btn-primary btn-lg'>Mở Giao diện Sinh viên</a><a href='login.php' class='btn btn-outline-secondary'>Đăng nhập Admin</a></div>";

} catch (PDOException $e) { echo "<div class='alert alert-danger'>❌ Lỗi: " . $e->getMessage() . "</div>"; }
echo "</div></body></html>";
?>