<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản lý Thư viện - Menu Chính</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary">HỆ THỐNG QUẢN LÝ THƯ VIỆN</h1>
            <p class="text-muted">Đại học Thủ đô Hà Nội - Nhóm 4 (Chủ đề 7)</p>
        </div>
        <div class="row text-center justify-content-center">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm p-4">
                    <h4>Tra cứu sách</h4>
                    <p class="text-muted small">Tìm kiếm nhanh theo mã, tên sách hoặc tác giả.</p>
                    <a href="timkiem.php" class="btn btn-primary">Truy cập</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm p-4">
                    <h4>Quản lý Sách</h4>
                    <p class="text-muted small">Thêm, sửa, xóa thông tin đầu sách trong kho.</p>
                    <a href="sach.php" class="btn btn-success">Truy cập</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm p-4">
                    <h4>Lập Phiếu Mượn</h4>
                    <p class="text-muted small">Quản lý nghiệp vụ mượn trả sách của độc giả.</p>
                    <a href="phieumuon.php" class="btn btn-warning text-white">Truy cập</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>