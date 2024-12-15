<?php
session_start();
require_once 'config.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate letter type
$valid_types = ['internal', 'external', 'directive', 'circular'];
if (!isset($_GET['type']) || !in_array($_GET['type'], $valid_types)) {
    die("Invalid letter type");
}

$type = $_GET['type'];
$user_id = $_SESSION['user_id'];

// Mapping for table and label names
$table_names = [
    'internal' => 'dms_internal_letters',
    'external' => 'dms_external_letters',
    'directive' => 'dms_directive_letters',
    'circular' => 'dms_circular_letters'
];

$type_labels = [
    'internal' => 'หนังสือภายใน',
    'external' => 'หนังสือภายนอก',
    'directive' => 'หนังสือสั่งการ',
    'circular' => 'หนังสือเวียน'
];

try {
    // Simplified query removing joins
    $query = "
        SELECT 
            subject, 
            date_created, 
            sender, 
            receiver,
            {$type}_number AS number, 
            {$type}_year AS year
        FROM {$table_names[$type]}
        WHERE created_by = :user_id
        ORDER BY date_created DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $letters = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดหนังสือ - <?= htmlspecialchars($type_labels[$type]) ?></title>
    <?php require_once 'header.php'; ?>
</head>
<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>รายละเอียด <?= htmlspecialchars($type_labels[$type]) ?></h1>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>กลับหน้าหลัก
            </a>
        </div>
        
        <?php if (!empty($letters)): ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ลำดับ</th>
                        <th>เลขที่หนังสือ</th>
                        <th>หัวเรื่อง</th>
                        <th>วันที่สร้าง</th>
                        <th>ผู้ส่ง</th>
                        <th>ผู้รับ</th>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <th>การดำเนินการ</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($letters as $index => $letter): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $letter['number'] . '/' . $letter['year'] ?></td>
                            <td><?= htmlspecialchars($letter['subject']) ?></td>
                            <td><?= date('d/m/Y', strtotime($letter['date_created'])) ?></td>
                            <td><?= htmlspecialchars($letter['sender']) ?></td>
                            <td><?= htmlspecialchars($letter['receiver']) ?></td>
                            
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="edit_<?= $type ?>_letter.php?id=<?= $letter['number'] ?>" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="confirmDelete(<?= $letter['number'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">ไม่มีข้อมูลสำหรับประเภทนี้</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(id) {
        if (confirm('คุณแน่ใจหรือว่าต้องการลบรายการนี้?')) {
            window.location.href = 'delete_<?= $type ?>_letter.php?id=' + id;
        }
    }
    </script>
</body>
</html>