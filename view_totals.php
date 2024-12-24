<?php
$conn = new mysqli("localhost", "root", "", "supplier_system");

// تهيئة المتغيرات
$month = '';
$year = '';
$supplier_totals = [];
$search_query = '';
$suppliers = [];

// جلب جميع الموردين من قاعدة البيانات لعرضهم في القائمة المنسدلة
$suppliers_result = $conn->query("SELECT * FROM suppliers");
while ($row = $suppliers_result->fetch_assoc()) {
    $suppliers[] = $row;
}

// معالجة الإرسال عند الضغط على زر البحث
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على بيانات البحث والشهر والسنة من الفورم
    $search_query = isset($_POST['search_query']) ? trim($_POST['search_query']) : '';
    $month = isset($_POST['month']) ? $_POST['month'] : '';
    $year = isset($_POST['year']) ? $_POST['year'] : '';

    if ($search_query !== '') {
        // جلب إجمالي الفواتير للمورد المحدد في الشهر والسنة المحددين
        $query = "SELECT * FROM invoices WHERE supplier_id = ? AND MONTH(invoice_date) = ? AND YEAR(invoice_date) = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $search_query, $month, $year);
        $stmt->execute();
        $invoices_result = $stmt->get_result();

        $totalAmount = 0;

        // حساب الإجمالي بناءً على نوع الفاتورة
        while ($invoice = $invoices_result->fetch_assoc()) {
            if ($invoice['type'] === 'اجل') {
                $totalAmount += $invoice['amount'];
            } elseif ($invoice['type'] === 'تحصيل') {
                $totalAmount -= $invoice['amount'];
            }
        }

        // حفظ الإجمالي للمورد المحدد
        $supplier_totals[$search_query] = $totalAmount;
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض إجمالي الفواتير</title>
    <style>
      
        .container { padding: 20px; text-align: right; }
        form { max-width: 500px; margin: auto; }
        label, select, input { display: block; width: 100%; margin-bottom: 10px; text-align: right; }
        button { padding: 10px; background: #5cb85c; color: white; border: none; cursor: pointer; }
        button:hover { background: #4cae4c; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: right; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
    </style>
        <link rel="stylesheet" href="styles.css">

</head>
<body>
    <!-- شريط التنقل -->
    <header>
        <a href="add_invoice.php">إضافة فاتورة</a>
        <a href="view_totals.php"> حسابات الموردين</a>
        <a href="manage_suppliers.php">إدارة الموردين</a>
        <a href="search_invoices.php">بحث في الفواتير</a>
        <a href="manage_expense_types.php">إدارة  المصروفات</a>
    
    <a href="view_monthly_expenses.php">المصاريف الشهرية</a>
    </header>
    <div class="container">
        <h1>عرض إجمالي الفواتير</h1>

        <!-- فورم البحث -->
        <form action="view_totals.php" method="POST">
            <select name="search_query" id="search_query" required>
                <option value="">-- اختر المورد --</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= $supplier['id'] ?>" <?= $search_query == $supplier['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($supplier['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="month">اختر الشهر:</label>
            <select name="month" id="month" required>
                <option value="1" <?= $month == 1 ? 'selected' : '' ?>>يناير</option>
                <option value="2" <?= $month == 2 ? 'selected' : '' ?>>فبراير</option>
                <option value="3" <?= $month == 3 ? 'selected' : '' ?>>مارس</option>
                <option value="4" <?= $month == 4 ? 'selected' : '' ?>>أبريل</option>
                <option value="5" <?= $month == 5 ? 'selected' : '' ?>>مايو</option>
                <option value="6" <?= $month == 6 ? 'selected' : '' ?>>يونيو</option>
                <option value="7" <?= $month == 7 ? 'selected' : '' ?>>يوليو</option>
                <option value="8" <?= $month == 8 ? 'selected' : '' ?>>أغسطس</option>
                <option value="9" <?= $month == 9 ? 'selected' : '' ?>>سبتمبر</option>
                <option value="10" <?= $month == 10 ? 'selected' : '' ?>>أكتوبر</option>
                <option value="11" <?= $month == 11 ? 'selected' : '' ?>>نوفمبر</option>
                <option value="12" <?= $month == 12 ? 'selected' : '' ?>>ديسمبر</option>
            </select>

            <label for="year">اختر السنة:</label>
            <input type="number" name="year" id="year" value="<?= $year ?>" required>

            <button type="submit">عرض الإجمالي</button>
        </form>

        <!-- عرض النتائج في جدول -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $search_query !== ''): ?>
            <h2>الإجمالي لشهر <?= date("F", mktime(0, 0, 0, $month, 10)) ?> سنة <?= $year ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>اسم المورد</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // عرض إجمالي المورد المحدد
                    foreach ($suppliers as $supplier) {
                        if ($supplier['id'] == $search_query) {
                            $totalAmount = isset($supplier_totals[$supplier['id']]) ? $supplier_totals[$supplier['id']] : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($supplier['name']) ?></td>
                                <td><?= number_format($totalAmount, 2) ?> جنيه</td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
