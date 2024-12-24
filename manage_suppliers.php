<?php
$conn = new mysqli("localhost", "root", "", "supplier_system");

// تهيئة المتغيرات
$error_message = '';
$success_message = '';

// معالجة إضافة مورد جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supplier_name'])) {
    $supplier_name = $_POST['supplier_name'];

    // التحقق من وجود اسم المورد مسبقًا
    $check_query = "SELECT * FROM suppliers WHERE name = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $supplier_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "اسم المورد موجود بالفعل. يرجى اختيار اسم آخر.";
    } else {
        // إدخال المورد الجديد
        $insert_query = "INSERT INTO suppliers (name) VALUES (?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("s", $supplier_name);
        if ($stmt->execute()) {
            $success_message = "تم إضافة المورد بنجاح.";
        } else {
            $error_message = "فشل في إضافة المورد. يرجى المحاولة مرة أخرى.";
        }
    }
}

// معالجة حذف المورد
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM suppliers WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    // إعادة توجيه إلى نفس الصفحة لتحديث القائمة
    header("Location: manage_suppliers.php");
    exit();
}

// جلب جميع الموردين للعرض
$suppliers = $conn->query("SELECT * FROM suppliers");

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الموردين</title>
    <style>
        .delete-btn {
            padding: 5px 10px;
            background-color: #d9534f;
            color: white;
            border: none;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <header>
        <a href="add_invoice.php">إضافة فاتورة</a>
        <a href="view_totals.php">حسابات الموردين</a>
        <a href="manage_suppliers.php">إدارة الموردين</a>
        <a href="search_invoices.php">بحث في الفواتير</a>
        <a href="manage_expense_types.php">إدارة المصروفات</a>
        <a href="view_monthly_expenses.php">المصاريف الشهرية</a>
    </header>

    <div class="container">
        <h1>إدارة الموردين</h1>

        <!-- رسائل النجاح أو الخطأ -->
        <?php if ($error_message): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="success"><?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>

        <!-- نموذج لإضافة مورد جديد -->
        <form action="manage_suppliers.php" method="POST">
            <label for="supplier_name">اسم المورد:</label>
            <input type="text" name="supplier_name" id="supplier_name" required>
            <button type="submit">إضافة المورد</button>
        </form>

        <!-- قائمة الموردين -->
        <h2>الموردون الحاليون</h2>
        <table>
            <thead>
                <tr>
                    <th>اسم المورد</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($supplier['name']) ?></td>
                        <td>
                            <a href="manage_suppliers.php?delete_id=<?= $supplier['id'] ?>" onclick="return confirm('هل أنت متأكد أنك تريد حذف هذا المورد؟')">
                                <button class="delete-btn">حذف</button>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
