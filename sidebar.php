<?php
// ตรวจสอบว่ามีการ start session หรือยัง
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// กำหนดหน้าปัจจุบัน เพื่อเน้นเมนูที่กำลังใช้งาน
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- เมนูหลัก -->
<div class="card mb-3">
    <div class="list-group list-group-flush">
        <a href="dashboard.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <!-- เพิ่ม Home icon -->
            <i class="bi bi-house-door"></i>
            หน้าหลัก
        </a>
        <div class="list-group-item list-group-item-action">
            <i class="bi bi-file-earmark-text me-2"></i>จัดการเอกสาร
            <div>
                <a href="internal_letters.php" class="list-group-item list-group-item-action small <?php echo ($current_page == 'internal_letters.php' || $current_page == 'add_internal_letter.php') ? 'active' : ''; ?>">หนังสือภายใน</a>
                <a href="external_letters.php" class="list-group-item list-group-item-action small <?php echo ($current_page == 'external_letters.php') ? 'active' : ''; ?>">หนังสือภายนอก</a>
                <a href="directive_letters.php" class="list-group-item list-group-item-action small <?php echo ($current_page == 'directive_letters.php') ? 'active' : ''; ?>">หนังสือสั่งการ</a>
                <a href="circular_letters.php" class="list-group-item list-group-item-action small <?php echo ($current_page == 'circular_letters.php') ? 'active' : ''; ?>">หนังสือเวียน</a>
                <a href="received_letters.php" class="list-group-item list-group-item-action small <?php echo ($current_page == 'received_letters.php') ? 'active' : ''; ?>">ทะเบียนรับหนังสือ</a>
            </div>
        </div>
    </div>
</div>

<!-- เมนูการจัดการสำหรับ Admin -->
<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
    <div class="card mb-3">
        <div class="card-header">
            <i class="bi bi-shield-lock me-2"></i>การจัดการระบบ
        </div>
        <div class="list-group list-group-flush">
            <a href="categories.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
                หมวดหมู่งาน
            </a>
            <div class="list-group-item list-group-item-action">
                <a href="user_management.php" class="list-group-item list-group-item-action small <?php echo ($current_page == 'user_management.php') ? 'active' : ''; ?>">จัดการผู้ใช้</a>
                <a href="system_logs.php" class="list-group-item list-group-item-action small <?php echo ($current_page == 'system_logs.php') ? 'active' : ''; ?>">บันทึกระบบ</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ปุ่มออกจากระบบ -->
<div class="card mb-3">
    <div class="list-group list-group-flush">
        <a href="logout.php" class="list-group-item list-group-item-action">
            <button class="btn btn-outline-primary w-100">
                <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
            </button>
        </a>
    </div>
</div>

<style>
    .card-header {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .card {
        border: 1px solid #dee2e6;
    }

    .list-group-item.active {
        background-color: #007bff;
        border-color: #007bff;
    }

    .list-group-item-action.small {
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
    }
</style>