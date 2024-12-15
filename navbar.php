<?php
// ตรวจสอบว่ามีการ start session หรือยัง
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">ระบบจัดการเอกสาร</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    ยินดีต้อนรับ, <?php echo $_SESSION['username']; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">ออกจากระบบ</a>
            </div>
        </div>
    </nav>