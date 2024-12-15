<?php
session_start();
require_once 'config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบสิทธิ์
if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// ดึงข้อมูลหมวดหมู่ที่ต้องแก้ไข
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM dms_categories WHERE category_id = :id");
    $stmt->execute(['id' => $category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        header("Location: categories.php");
        exit();
    }
}

// แก้ไขหมวดหมู่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'] ?? null;

    try {
        $stmt = $pdo->prepare("UPDATE dms_categories SET category_name = :name, description = :desc WHERE category_id = :id");
        $stmt->execute([
            'name' => $category_name, 
            'desc' => $description,
            'id' => $category_id
        ]);
        
        header("Location: categories.php?updated=1");
        exit();
    } catch(PDOException $e) {
        $error = "ไม่สามารถแก้ไขหมวดหมู่ได้: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>แก้ไขหมวดหมู่งาน - DMS</title>
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
                <div class="card-header">
                    <h3>แก้ไขหมวดหมู่งาน</h3>
                </div>
                
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">ชื่อหมวดหมู่</label>
                            <input type="text" name="category_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">คำอธิบาย (ถ้ามี)</label>
                            <textarea name="description" class="form-control" rows="3"><?php 
                                echo htmlspecialchars($category['description'] ?? ''); 
                            ?></textarea>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="categories.php" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>