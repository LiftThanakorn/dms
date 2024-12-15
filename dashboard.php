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
    // นับจำนวนหนังสือแต่ละประเภท
    $letter_types = [
        'internal' => 'dms_internal_letters',
        'external' => 'dms_external_letters',
        'directive' => 'dms_directive_letters',
        'circular' => 'dms_circular_letters'
    ];

    $letter_counts = [];
    $recent_letters = [];

    foreach ($letter_types as $type => $table) {
        // นับจำนวนหนังสือแต่ละประเภท
        $count_query = "SELECT COUNT(*) AS total_letters FROM $table WHERE created_by = :user_id";
        $count_stmt = $pdo->prepare($count_query);
        $count_stmt->execute(['user_id' => $user_id]);
        $letter_counts[$type] = $count_stmt->fetch(PDO::FETCH_ASSOC)['total_letters'];
    }


    // ดึงข้อมูลหนังสือล่าสุดจากทุกประเภท
    $recent_query = "
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
    LIMIT 5
    ";

    $recent_stmt = $pdo->prepare($recent_query);
    $recent_stmt->execute(['user_id' => $user_id]);
    $recent_letters = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - DMS</title>
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
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Dashboard
                    </div>
                    <div class="card-body">
                        <h5>สวัสดี, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>!</h5>
                        <p>ยินดีต้อนรับสู่ระบบจัดการเอกสาร</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        สรุปจำนวนหนังสือ
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($letter_counts as $type => $count): ?>
                                <div class="col-md-3">
                                    <div class="card text-center mb-3">
                                        <div class="card-body">
                                            <h5>
                                                <?php
                                                $type_labels = [
                                                    'internal' => 'หนังสือภายใน',
                                                    'external' => 'หนังสือภายนอก',
                                                    'directive' => 'หนังสือสั่งการ',
                                                    'circular' => 'หนังสือเวียน'
                                                ];
                                                echo $type_labels[$type];
                                                ?>
                                            </h5>
                                            <h2>
                                                <a href="letter_details.php?type=<?php echo $type; ?>" class="stretched-link text-decoration-none">
                                                    <?php echo $count; ?>
                                                </a>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        หนังสือล่าสุด
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_letters)): ?>
                            <div class="list-group">
                                <?php foreach ($recent_letters as $letter): ?>
                                    <a href="<?php echo $letter['type'] . '_letter_detail.php?id=' . $letter['id']; ?>"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                <?php
                                                $type_labels = [
                                                    'internal' => 'หนังสือภายใน',
                                                    'external' => 'หนังสือภายนอก',
                                                    'directive' => 'หนังสือสั่งการ',
                                                    'circular' => 'หนังสือเวียน'
                                                ];
                                                echo htmlspecialchars($type_labels[$letter['type']] . ': ' . $letter['subject']);
                                                ?>
                                            </h5>
                                            <small><?php echo htmlspecialchars($letter['date_created']); ?></small>
                                        </div>
                                        <p class="mb-1">
                                            ถึง: <?php echo htmlspecialchars($letter['receiver']); ?>
                                            | เลขที่: <?php echo htmlspecialchars($letter['number'] . '/' . $letter['year']); ?>
                                        </p>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="all_letters.php" class="btn btn-outline-primary">ดูทั้งหมด</a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">คุณยังไม่มีหนังสือ</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>