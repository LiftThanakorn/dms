<?php
session_start();
require_once 'config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// จัดการการลบหนังสือ
if (isset($_GET['delete'])) {
    $internal_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM dms_internal_letters WHERE internal_id = ?");

    try {
        $stmt->execute([$internal_id]);
        $_SESSION['message'] = "ลบหนังสือภายในสำเร็จ";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบหนังสือ: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
    header("Location: internal_letters.php");
    exit();
}

// ดึงข้อมูลหนังสือภายใน
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';

// สร้าง Query สำหรับค้นหาและกรอง
$query = "SELECT il.*, c.category_name, u.username 
          FROM dms_internal_letters il
          LEFT JOIN dms_categories c ON il.category_id = c.category_id
          LEFT JOIN dms_users u ON il.created_by = u.user_id
          WHERE 1=1 ";

$params = [];

if (!empty($search)) {
    $query .= " AND (il.subject LIKE :search OR il.sender LIKE :search OR il.receiver LIKE :search)";
    $search_param = "%{$search}%";
    $params['search'] = $search_param;
}

if (!empty($category_filter)) {
    $query .= " AND il.category_id = :category";
    $params['category'] = $category_filter;
}

$query .= " ORDER BY il.date_created DESC";

$stmt = $pdo->prepare($query);

// Bind parameters
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

$stmt->execute();
$internal_letters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงหมวดหมู่สำหรับ Dropdown
$categories_stmt = $pdo->query("SELECT * FROM dms_categories");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>หนังสือภายใน - ระบบจัดการเอกสาร</title>
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
                <?php
                // แสดงข้อความแจ้งเตือน
                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-{$_SESSION['message_type']}'>{$_SESSION['message']}</div>";
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }
                ?>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>หนังสือภายใน</h4>
                        <!-- ให้ทุก role สามารถเข้าถึงปุ่มนี้ได้ -->
                        <a href="add_internal_letter.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> เพิ่มหนังสือภายใน
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- ฟอร์มค้นหาและกรอง -->
                        <form method="get" action="internal_letters.php" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="ค้นหาหนังสือ"
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-4">
                                    <select name="category" class="form-select">
                                        <option value="">เลือกหมวดหมู่ทั้งหมด</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['category_id']; ?>"
                                                <?php echo ($category_filter == $category['category_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> ค้นหา
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- ตารางหนังสือภายใน -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>เลขที่หนังสือ</th>
                                        <th>หัวเรื่อง</th>
                                        <th>หมวดหมู่</th>
                                        <th>วันที่สร้าง</th>
                                        <th>ผู้รับ</th>
                                        <th>ผู้สร้าง</th> <!-- เพิ่มคอลัมน์สำหรับแสดงผู้สร้าง -->
                                        <!-- เฉพาะ admin ที่มีสิทธิ์ลบ -->
                                        <?php if ($_SESSION['role'] == 'admin'): ?>
                                            <th>การดำเนินการ</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($internal_letters as $letter): ?>
                                        <tr>
                                            <td><?php echo $letter['internal_number'] . '/' . $letter['internal_year']; ?></td>
                                            <td><?php echo htmlspecialchars($letter['subject']); ?></td>
                                            <td><?php echo htmlspecialchars($letter['category_name'] ?? 'ไม่มีหมวดหมู่'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($letter['date_created'])); ?></td>
                                            <td><?php echo htmlspecialchars($letter['receiver']); ?></td>
                                            <td><?php echo htmlspecialchars($letter['username']); ?></td> <!-- แสดงชื่อผู้สร้าง -->
                                            <!-- เฉพาะ admin ที่สามารถลบ -->
                                            <?php if ($_SESSION['role'] == 'admin'): ?>

                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <!-- ปุ่มแก้ไข -->
                                                        <a href="edit_internal_letter.php?id=<?php echo $letter['internal_id']; ?>"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <!-- ปุ่มลบ -->
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete(<?php echo $letter['internal_id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function confirmDelete(id) {
            if (confirm('คุณต้องการลบหนังสือนี้ ใช่หรือไม่?')) {
                // เปลี่ยนเส้นทางไปยังหน้าลบ พร้อมส่ง ID
                window.location.href = 'internal_letters.php?delete=' + id;
            }
        }
    </script>

</body>

</html>