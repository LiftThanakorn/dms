<?php
session_start();
require_once 'config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบ ID ของหนังสือ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$letter_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // ดึงรายละเอียดหนังสือ
    $query = "SELECT il.*, c.category_name 
              FROM dms_internal_letters il
              LEFT JOIN dms_categories c ON il.category_id = c.category_id
              WHERE il.internal_id = :letter_id AND il.created_by = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'letter_id' => $letter_id,
        'user_id' => $user_id
    ]);
    $letter = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$letter) {
        // ถ้าไม่พบหนังสือหรือไม่ใช่ของผู้ใช้
        header("Location: dashboard.php");
        exit();
    }
} catch(PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>รายละเอียดหนังสือภายใน - DMS</title>
    <?php require_once 'header.php'; ?>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once 'sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        รายละเอียดหนังสือภายใน
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3><?php echo htmlspecialchars($letter['subject']); ?></h3>
                                <p><strong>เลขที่หนังสือ:</strong> <?php echo htmlspecialchars($letter['internal_number'] . '/' . $letter['internal_year']); ?></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p><strong>วันที่สร้าง:</strong> <?php echo htmlspecialchars($letter['date_created']); ?></p>
                                <p><strong>หมวดหมู่:</strong> <?php echo htmlspecialchars($letter['category_name'] ?? 'ไม่ระบุ'); ?></p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ผู้ส่ง:</strong> <?php echo htmlspecialchars($letter['sender']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>ผู้รับ:</strong> <?php echo htmlspecialchars($letter['receiver']); ?></p>
                            </div>
                        </div>

                        <?php if (!empty($letter['note'])): ?>
                            <div class="mt-3">
                                <strong>บันทึกเพิ่มเติม:</strong>
                                <p><?php echo htmlspecialchars($letter['note']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($letter['file_name'])): ?>
                            <div class="mt-3">
                                <strong>เอกสารแนบ:</strong>
                                <a href="download.php?file=<?php echo urlencode($letter['file_name']); ?>" class="btn btn-sm btn-outline-primary">
                                    ดาวน์โหลด
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="dashboard.php" class="btn btn-secondary">กลับหน้าหลัก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>