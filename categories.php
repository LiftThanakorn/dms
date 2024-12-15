<?php
session_start();
require_once 'config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ฟังก์ชันลบหมวดหมู่
if (isset($_GET['delete']) && $_SESSION['role'] == 'admin') {
    $category_id = $_GET['delete'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM dms_categories WHERE category_id = :id");
        $stmt->execute(['id' => $category_id]);
        header("Location: categories.php?deleted=1");
        exit();
    } catch(PDOException $e) {
        $error = "ไม่สามารถลบหมวดหมู่ได้: " . $e->getMessage();
    }
}

// ดึงรายการหมวดหมู่
$stmt = $pdo->query("SELECT * FROM dms_categories ORDER BY category_id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>จัดการหมวดหมู่งาน - DMS</title>
    <?php require_once 'header.php'; ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">ระบบจัดการเอกสาร</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text text-white me-3">
                ยินดีต้อนรับ, <?php echo $_SESSION['username']; ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>จัดการหมวดหมู่งาน</h3>
                    <a href="add_category.php" class="btn btn-success">
                        <i class="bi bi-plus"></i> เพิ่มหมวดหมู่
                    </a>
                </div>
                
                <?php if(isset($_GET['deleted'])): ?>
                    <div class="alert alert-success">ลบหมวดหมู่สำเร็จ</div>
                <?php endif; ?>
                
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>รหัส</th>
                                    <th>ชื่อหมวดหมู่</th>
                                    <th>คำอธิบาย</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['category_id']); ?></td>
                                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description'] ?? '-'); ?></td>
                                    <td>
                                        <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" 
                                           class="btn btn-sm btn-primary">แก้ไข</a>
                                        
                                        <?php if($_SESSION['role'] == 'admin'): ?>
                                        <a href="categories.php?delete=<?php echo $category['category_id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบหมวดหมู่นี้?')">ลบ</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if(empty($categories)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">ไม่มีหมวดหมู่งาน</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>