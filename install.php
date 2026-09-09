<?php
$host = 'localhost'; $username = 'root'; $password = '';
echo "<!DOCTYPE html><html lang='vi'><head><meta charset='UTF-8'><title>Cài đặt Dữ liệu Toàn diện</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='bg-light d-flex justify-content-center align-items-center' style='min-height: 100vh;'><div class='card shadow-lg p-5 my-4' style='max-width: 800px; width: 100%;'><h2 class='text-primary fw-bold text-center mb-4'>Hệ Thống Quản Lý Thư Viện (Bản Toàn Diện)</h2>";

try {
    // 1. KẾT NỐI VÀ RESET DATABASE
    $conn = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("DROP DATABASE IF EXISTS quanlythuvien");
    $conn->exec("CREATE DATABASE quanlythuvien CHARACTER SET utf8 COLLATE utf8_general_ci");
    $conn->exec("USE quanlythuvien");

    // 2. KHỞI TẠO CẤU TRÚC 8 BẢNG CHUẨN ERD
    $tables = [
        "CREATE TABLE admin (taikhoan VARCHAR(50) PRIMARY KEY, matkhau VARCHAR(255))",
        "CREATE TABLE theloai (ma_the_loai INT AUTO_INCREMENT PRIMARY KEY, ten_the_loai VARCHAR(100) NOT NULL)",
        "CREATE TABLE sach (ma_sach VARCHAR(50) PRIMARY KEY, ten_sach VARCHAR(255) NOT NULL, tac_gia VARCHAR(100), so_luong INT, ma_the_loai INT, FOREIGN KEY (ma_the_loai) REFERENCES theloai(ma_the_loai))",
        "CREATE TABLE bandoc (ma_sv VARCHAR(50) PRIMARY KEY, ten_sv VARCHAR(100) NOT NULL, lop VARCHAR(50))",
        "CREATE TABLE phieumuon (ma_phieu VARCHAR(50) PRIMARY KEY, ma_sv VARCHAR(50), ngay_tao DATE, ngay_hen_tra DATE, trang_thai VARCHAR(50), FOREIGN KEY (ma_sv) REFERENCES bandoc(ma_sv))",
        "CREATE TABLE chitietphieu (ma_phieu VARCHAR(50), ma_sach VARCHAR(50), so_luong INT, PRIMARY KEY (ma_phieu, ma_sach), FOREIGN KEY (ma_phieu) REFERENCES phieumuon(ma_phieu), FOREIGN KEY (ma_sach) REFERENCES sach(ma_sach))",
        "CREATE TABLE phieutra (ma_phieu_tra VARCHAR(50) PRIMARY KEY, ma_sv VARCHAR(50), ngay_tra_thuc_te DATE, ngay_hen_tra DATE, trang_thai VARCHAR(50), FOREIGN KEY (ma_sv) REFERENCES bandoc(ma_sv))",
        "CREATE TABLE bien_ban_phat (ma_phieu_phat VARCHAR(50) PRIMARY KEY, ma_sv VARCHAR(50), ngay_lap DATE, ly_do TEXT, tien_phat DECIMAL(10,2), FOREIGN KEY (ma_sv) REFERENCES bandoc(ma_sv))"
    ];
    foreach ($tables as $sql) {
        $conn->exec($sql);
    }

    // 3. THÊM TÀI KHOẢN ADMIN & THỂ LOẠI
    $conn->exec("INSERT INTO admin (taikhoan, matkhau) VALUES ('admin', '123')");
    $conn->exec("INSERT INTO theloai (ten_the_loai) VALUES ('Mạng & Viễn thông'), ('Hệ thống Nhúng & IoT'), ('Lập trình Phần mềm'), ('Luật & Kinh tế'), ('Kỹ năng Doanh nghiệp')");
    
    // 4. THÊM 20 ĐẦU SÁCH SIÊU ĐA DẠNG
    $conn->exec("INSERT INTO sach (ma_sach, ten_sach, tac_gia, so_luong, ma_the_loai) VALUES 
        ('S01', 'Thiết kế mạng LAN với Cisco Packet Tracer', 'Trần Mạng', 15, 1),
        ('S02', 'Định tuyến cơ bản: VLAN, DHCP và Static Routing', 'NXB Bách Khoa', 12, 1),
        ('S03', 'Tự học Arduino từ cơ bản đến nâng cao', 'Vũ Arduino', 20, 2),
        ('S04', 'Lập trình Vi điều khiển ESP32 và ESP8266', 'NXB Khoa học', 14, 2),
        ('S05', 'Phát triển ứng dụng IoT với ESP32-S3', 'Tech Studio', 8, 2),
        ('S06', 'Lập trình Java - Xây dựng ứng dụng quản lý kho', 'Lê Code', 10, 3),
        ('S07', 'Cấu trúc dữ liệu và Giải thuật', 'Nguyễn Văn A', 25, 3),
        ('S08', 'Luật Tố tụng Hành chính (TTHC) 2015', 'NXB Chính trị', 30, 4),
        ('S09', 'Luật Doanh nghiệp và Hợp đồng lao động', 'Luật sư Trần', 15, 4),
        ('S10', 'Hệ thống tự động hóa: Băng chuyền phân loại màu', 'NXB Kỹ Thuật', 7, 2),
        ('S11', 'Phát triển Robot AI trợ lý ảo', 'NXB Công Nghệ', 5, 2),
        ('S12', 'Cẩm nang thực tập sinh tại Doanh nghiệp Công nghệ', 'VSD Tech', 22, 5),
        ('S13', 'Linh kiện điện tử và Mạch điện cơ bản', 'Nguyễn Mạch', 18, 2),
        ('S14', 'Thiết kế UI/UX thực chiến với Figma', 'Phạm UX', 10, 3),
        ('S15', 'Quản trị Cơ sở dữ liệu MySQL Server', 'Hoàng SQL', 12, 3)");

    // 5. NẠP DANH SÁCH SINH VIÊN (Nhóm 4 + SV Khác)
    $lop_chinh = '30INF048_CNTT D2024_2';
    $conn->exec("INSERT INTO bandoc (ma_sv, ten_sv, lop) VALUES 
        ('224001795', 'Nguyễn Việt Hùng', '$lop_chinh'),
        ('224001816', 'Trương Văn Minh', '$lop_chinh'),
        ('224001785', 'Tô Thị Thu Hiền', '$lop_chinh'),
        ('224001793', 'Lê Việt Hùng', '$lop_chinh'),
        ('224001822', 'Ôn Ngọc Phi', '$lop_chinh'),
        ('SV006', 'Hoàng Thái Tuấn', 'D2024_Kế toán'),
        ('SV007', 'Nguyễn Mai Anh', 'D2024_Luật'),
        ('SV008', 'Trần Hữu Kiên', 'D2024_Điện tử')");

    // 6. TẠO CÁC KỊCH BẢN MƯỢN TRẢ & PHẠT
    $today = date('Y-m-d');
    $future_date = date('Y-m-d', strtotime('+5 days'));
    $past_date_1 = date('Y-m-d', strtotime('-12 days'));
    $past_date_2 = date('Y-m-d', strtotime('-3 days'));
    
    // Khởi tạo Phiếu Mượn
    $conn->exec("INSERT INTO phieumuon (ma_phieu, ma_sv, ngay_tao, ngay_hen_tra, trang_thai) VALUES 
        ('PM_001', '224001795', '$past_date_1', '$past_date_2', 'Quá hạn'),          
        ('PM_002', '224001816', '$today', '$future_date', 'Đang mượn'),            
        ('PM_003', '224001785', '$past_date_1', '$past_date_2', 'Đã trả (Đã nộp phạt)'), 
        ('PM_004', '224001793', '$past_date_1', '$today', 'Đã trả'),               
        ('YC_005', '224001822', '$today', NULL, 'Chờ duyệt'),                      
        ('PM_006', 'SV007', '$today', '$future_date', 'Đang mượn')                 
    ");

    // Gắn Sách vào Chi Tiết Phiếu
    $conn->exec("INSERT INTO chitietphieu (ma_phieu, ma_sach, so_luong) VALUES 
        ('PM_001', 'S04', 1),
        ('PM_002', 'S01', 1),
        ('PM_003', 'S06', 1),
        ('PM_004', 'S10', 1),
        ('YC_005', 'S11', 1),
        ('PM_006', 'S08', 1)
    ");

    // Lập 1 Biên bản phạt thực tế để hiển thị lên bảng Thống Kê
    $conn->exec("INSERT INTO bien_ban_phat (ma_phieu_phat, ma_sv, ngay_lap, ly_do, tien_phat) VALUES 
        ('PM_003', '224001785', '$today', 'Sách quá hạn trả 9 ngày', 45000)
    ");
    
    // Ghi nhận 1 Phiếu Trả vào bảng phieutra để đáp ứng đủ yêu cầu ERD
    $conn->exec("INSERT INTO phieutra (ma_phieu_tra, ma_sv, ngay_tra_thuc_te, ngay_hen_tra, trang_thai) VALUES 
        ('PT_004', '224001793', '$today', '$today', 'Đã trả đúng hạn')
    ");

    echo "<div class='alert alert-success text-center'>
            <h4 class='alert-heading mb-3'>🎉 Khởi Tạo Thành Công!</h4>
            <p>Đã nạp 8 bảng CSDL chuẩn, 5 Thể loại, 15 Đầu sách chuyên ngành.</p>
            <p>Đã tự động nạp danh sách 5 thành viên Nhóm 4 lớp $lop_chinh.</p>
            <p>Đã giả lập toàn bộ kịch bản: Mượn sách, Quá hạn, Đã trả và Đóng phạt.</p>
          </div>
          <div class='d-grid gap-3 mt-4'>
            <a href='index.php' class='btn btn-primary btn-lg'>Mở Giao diện Sinh viên</a>
            <a href='admin.php' class='btn btn-dark btn-lg'>Vào Khu vực Admin & Thống kê</a>
          </div>";

} catch (PDOException $e) { 
    echo "<div class='alert alert-danger'>
            <h4 class='alert-heading'>❌ Lỗi Khởi Tạo</h4>
            <p>" . $e->getMessage() . "</p>
          </div>"; 
}
echo "</div></body></html>";
?>