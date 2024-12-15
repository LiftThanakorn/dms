<?php
session_start();
require_once 'config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// ดึงรายการหมวดหมู่
$categories_stmt = $pdo->query("SELECT * FROM dms_categories");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// สร้างเลขที่หนังสือภายในอัตโนมัติ
$stmt = $pdo->query("
    SELECT COALESCE(MAX(internal_number), 0) + 1 as next_number 
    FROM dms_internal_letters 
    WHERE internal_year = " . (date('Y') + 543)
);
$next_internal_number = $stmt->fetchColumn();

// ประมวลผลฟอร์มเมื่อส่งข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รวบรวมข้อมูลจากฟอร์ม
    $internal_number = $next_internal_number;
    $internal_year = date('Y') + 543;
    $subject = $_POST['subject'];
    $category_id = $_POST['category_id'];
    $sender = 'งานบริหารทรัพยากรบุคคลและนิติการ';
    $receiver = $_POST['receiver'];
    $date_created = date('Y-m-d');
    $note = $_POST['note'] ?? null;

    // จัดการไฟล์แนบ
    $file_name = null;
    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = 'uploads/internal_letters/';
        
        // สร้างโฟลเดอร์หากยังไม่มี
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $original_name = $_FILES['attachment']['name'];
        $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $file_name = 'internal_' . uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . $file_name;

        // ย้ายไฟล์
        if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
            $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัพโหลดไฟล์";
            $_SESSION['message_type'] = "danger";
            header("Location: add_internal_letter.php");
            exit();
        }
    }

    // เตรียม SQL สำหรับบันทึกข้อมูล
    $stmt = $pdo->prepare("
        INSERT INTO dms_internal_letters 
        (internal_number, internal_year, subject, category_id, sender, 
        receiver, date_created, file_name, note, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    try {
        $result = $stmt->execute([
            $internal_number,
            $internal_year,
            $subject,
            $category_id,
            $sender,
            $receiver,
            $date_created,
            $file_name,
            $note,
            $_SESSION['user_id']
        ]);

        if ($result) {
            $_SESSION['message'] = "เพิ่มหนังสือภายในสำเร็จ";
            $_SESSION['message_type'] = "success";
            header("Location: internal_letters.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// แปลงวันที่เป็นรูปแบบไทย
function thai_date($date) {
    $months = [
        '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', 
        '04' => 'เมษายน', '05' => 'พฤษภาคม', '06' => 'มิถุนายน', 
        '07' => 'กรกฎาคม', '08' => 'สิงหาคม', '09' => 'กันยายน', 
        '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
    ];
    
    $d = date('d', strtotime($date));
    $m = $months[date('m', strtotime($date))];
    $y = date('Y', strtotime($date)) + 543;
    
    return "$d $m $y";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
   <meta charset="UTF-8">
   <title>เพิ่มหนังสือภายใน - ระบบจัดการเอกสาร</title>
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
               if (isset($_SESSION['message'])) {
                   echo "<div class='alert alert-{$_SESSION['message_type']}'>{$_SESSION['message']}</div>";
                   unset($_SESSION['message']);
                   unset($_SESSION['message_type']);
               }
               ?>
               
               <div class="card">
                   <div class="card-header bg-primary text-white">
                       <h4 class="mb-0">เพิ่มหนังสือภายใน</h4>
                   </div>
                   
                   <div class="card-body">
                       <form method="post" enctype="multipart/form-data">
                           <div class="row">
                               <div class="col-md-6 mb-3">
                                   <label class="form-label">เลขที่หนังสือ</label>
                                   <div class="form-control bg-light">
                                       <?php echo $next_internal_number . '/' . (date('Y') + 543); ?>
                                   </div>
                               </div>
                               
                               <div class="col-md-6 mb-3">
                                   <label class="form-label">วันที่สร้าง</label>
                                   <input type="text" class="form-control" 
                                          value="<?php echo thai_date(date('Y-m-d')); ?>" 
                                          readonly>
                               </div>
                           </div>

                           <div class="row">
                               <div class="col-md-6 mb-3">
                                   <label class="form-label">หัวเรื่อง <span class="text-danger">*</span></label>
                                   <input type="text" name="subject" class="form-control" required placeholder="กรอกหัวเรื่องหนังสือ">
                               </div>

                               <div class="col-md-6 mb-3">
                                   <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                                   <select name="category_id" class="form-select" required>
                                       <option value="">เลือกหมวดหมู่</option>
                                       <?php foreach ($categories as $category): ?>
                                           <option value="<?php echo $category['category_id']; ?>">
                                               <?php echo htmlspecialchars($category['category_name']); ?>
                                           </option>
                                       <?php endforeach; ?>
                                   </select>
                               </div>
                           </div>

                           <div class="row">
                               <div class="col-md-6 mb-3">
                                   <label class="form-label">ผู้รับ <span class="text-danger">*</span></label>
                                   <input type="text" name="receiver" class="form-control" required placeholder="กรอกชื่อผู้รับหนังสือ">
                               </div>

                               <div class="col-md-6 mb-3">
                                   <label class="form-label">หมายเหตุ</label>
                                   <textarea name="note" class="form-control" rows="3" placeholder="เพิ่มหมายเหตุ (ถ้ามี)"></textarea>
                               </div>
                           </div>

                           <div class="d-grid">
                               <button type="submit" class="btn btn-primary">
                                   <i class="fas fa-save"></i> บันทึกหนังสือภายใน
                               </button>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
   <script>
   document.addEventListener('DOMContentLoaded', function() {
       // ตรวจสอบขนาดไฟล์
       const attachmentInput = document.querySelector('input[name="attachment"]');
       if (attachmentInput) {
           attachmentInput.addEventListener('change', function(e) {
               const file = e.target.files[0];
               const maxSize = 5 * 1024 * 1024; // 5MB

               if (file && file.size > maxSize) {
                   alert('กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5MB');
                   e.target.value = ''; // ล้างค่าไฟล์
               }
           });
       }
   });
   </script>
</body>
</html>

