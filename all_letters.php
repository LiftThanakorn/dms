<?php
session_start();
require_once 'config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลหนังสือทั้งหมดที่ผู้ใช้สร้าง
$user_id = $_SESSION['user_id'];

try {
    $all_letters_query = "
    SELECT 'internal' AS type, internal_id AS id, subject, date_created, sender, receiver, internal_number AS number, internal_year AS year 
    FROM dms_internal_letters 
    WHERE created_by = :user_id
    UNION ALL
    SELECT 'external' AS type, external_id AS id, subject, date_created, sender, receiver, external_number AS number, external_year AS year 
    FROM dms_external_letters 
    WHERE created_by = :user_id
    UNION ALL
    SELECT 'directive' AS type, directive_id AS id, subject, date_created, sender, receiver, directive_number AS number, directive_year AS year 
    FROM dms_directive_letters 
    WHERE created_by = :user_id
    UNION ALL
    SELECT 'circular' AS type, circular_id AS id, subject, date_created, sender, receiver, circular_number AS number, circular_year AS year 
    FROM dms_circular_letters 
    WHERE created_by = :user_id
    ORDER BY date_created DESC
    ";

    $stmt = $pdo->prepare($all_letters_query);
    $stmt->execute(['user_id' => $user_id]);
    $all_letters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>All Letters - DMS</title>
    <?php require_once 'header.php'; ?>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>หนังสือทั้งหมด</h1>
            <a href="javascript:history.back()" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-2"></i>กลับหน้าหลัก</a>

            </a>
        </div>
        <?php if (!empty($all_letters)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ประเภทหนังสือ</th>
                        <th>เรื่อง</th>
                        <th>วันที่</th>
                        <th>ผู้ส่ง</th>
                        <th>ผู้รับ</th>
                        <th>เลขที่/ปี</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_letters as $index => $letter): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>
                                <?php
                                $type_labels = [
                                    'internal' => 'หนังสือภายใน',
                                    'external' => 'หนังสือภายนอก',
                                    'directive' => 'หนังสือสั่งการ',
                                    'circular' => 'หนังสือเวียน'
                                ];
                                echo htmlspecialchars($type_labels[$letter['type']]);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($letter['subject']); ?></td>
                            <td><?php echo htmlspecialchars($letter['date_created']); ?></td>
                            <td><?php echo htmlspecialchars($letter['sender']); ?></td>
                            <td><?php echo htmlspecialchars($letter['receiver']); ?></td>
                            <td><?php echo htmlspecialchars($letter['number'] . '/' . $letter['year']); ?></td>
                            <td>
                                <a href="<?php echo $letter['type'] . '_letter_detail.php?id=' . $letter['id']; ?>"
                                    class="btn btn-primary btn-sm">ดูรายละเอียด</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">ไม่มีหนังสือที่คุณสร้าง</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>