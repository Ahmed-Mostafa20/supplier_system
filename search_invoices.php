<?php
$conn = new mysqli("localhost", "root", "", "supplier_system");

// تهيئة المتغيرات
$from_date = '';
$to_date = '';
$supplier_id = '';
$supplier_filter = 'all'; // القيمة الافتراضية "الكل"
$invoices = [];

// جلب الموردين للقائمة المنسدلة
$suppliers_result = $conn->query("SELECT * FROM suppliers");

// معالجة إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';
    $supplier_filter = $_POST['supplier_filter'] ?? 'all';
    $supplier_id = $_POST['supplier_id'] ?? '';

    // بناء الاستعلام ديناميكياً بناءً على الفلتر
    $query = "SELECT i.*, s.name AS supplier_name FROM invoices i 
              INNER JOIN suppliers s ON i.supplier_id = s.id 
              WHERE i.invoice_date BETWEEN ? AND ?";

    if ($supplier_filter === 'one' && $supplier_id !== '') {
        $query .= " AND i.supplier_id = ?";
    }

    $stmt = $conn->prepare($query);

    if ($supplier_filter === 'one' && $supplier_id !== '') {
        $stmt->bind_param("ssi", $from_date, $to_date, $supplier_id);
    } else {
        $stmt->bind_param("ss", $from_date, $to_date);
    }

    $stmt->execute();
    $invoices_result = $stmt->get_result();

    // جمع النتائج
    while ($row = $invoices_result->fetch_assoc()) {
        $invoices[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بحث في الفواتير</title>
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
        <h1>بحث في الفواتير</h1>

        <!-- نموذج البحث -->
        <form action="search_invoices.php" method="POST">
            <label for="from_date">من تاريخ:</label>
            <input type="date" name="from_date" id="from_date" value="<?= htmlspecialchars($from_date) ?>" required>

            <label for="to_date">إلى تاريخ:</label>
            <input type="date" name="to_date" id="to_date" value="<?= htmlspecialchars($to_date) ?>" required>

            <label for="supplier_filter">بحث عن:</label>
            <select name="supplier_filter" id="supplier_filter" required onchange="toggleSupplierDropdown(this.value)">
                <option value="all" <?= $supplier_filter === 'all' ? 'selected' : '' ?>>كل الموردين</option>
                <option value="one" <?= $supplier_filter === 'one' ? 'selected' : '' ?>>مورد محدد</option>
            </select>

            <div id="supplier_dropdown" style="display: <?= $supplier_filter === 'one' ? 'block' : 'none' ?>;">
                <label for="supplier_id">اختر المورد:</label>
                <select name="supplier_id" id="supplier_id">
                    <option value="">-- اختر المورد --</option>
                    <?php while ($supplier = $suppliers_result->fetch_assoc()): ?>
                        <option value="<?= $supplier['id'] ?>" <?= $supplier_id == $supplier['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($supplier['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit">بحث</button>
        </form>

        <!-- عرض الفواتير في جدول -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <h2>الفواتير</h2>
            <table>
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>المورد</th>
                        <th>المبلغ</th>
                        <th>النوع</th>
                        <th>الإجمالي بعد الفاتورة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $running_total = 0;

                    foreach ($invoices as $invoice) {
                        if ($invoice['type'] === 'اجل') {
                            $running_total += $invoice['amount'];
                        } elseif ($invoice['type'] === 'تحصيل') {
                            $running_total -= $invoice['amount'];
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($invoice['invoice_date']) ?></td>
                            <td><?= htmlspecialchars($invoice['supplier_name']) ?></td>
                            <td><?= number_format($invoice['amount'], 2) ?> جنيه</td>
                            <td><?= htmlspecialchars($invoice['type']) ?></td>
                            <td><?= number_format($running_total, 2) ?> جنيه</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function toggleSupplierDropdown(value) {
            const dropdown = document.getElementById('supplier_dropdown');
            dropdown.style.display = value === 'one' ? 'block' : 'none';
        }
    </script>
</body>
</html>
