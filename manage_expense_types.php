<?php
$conn = new mysqli("localhost", "root", "", "supplier_system");

// Initialize variables
$message = '';

// Handle adding a new expense type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_type'])) {
    $type = $_POST['type'];

    if ($type) {
        $stmt = $conn->prepare("INSERT INTO expense_types (type_name) VALUES (?)");
        $stmt->bind_param("s", $type);

        if ($stmt->execute()) {
            $message = "تم إضافة نوع المصروف بنجاح.";
        } else {
            $message = "خطأ: نوع المصروف موجود بالفعل أو المدخل غير صحيح.";
        }
    } else {
        $message = "يرجى تعبئة نوع المصروف.";
    }
}

// Handle deleting an expense type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_type'])) {
    $type_id = $_POST['type_id'];
    $stmt = $conn->prepare("DELETE FROM expense_types WHERE id = ?");
    $stmt->bind_param("i", $type_id);
    $stmt->execute();
    $message = "تم حذف نوع المصروف بنجاح.";
    header("Location: manage_expense_types.php");
    exit();
}

// Fetch predefined expense types
$types = [];
$result = $conn->query("SELECT * FROM expense_types ORDER BY type_name ASC");
while ($row = $result->fetch_assoc()) {
    $types[] = $row;
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة أنواع المصروفات</title>
    <link rel="stylesheet" href="styles.css">
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
        <h1>إدارة أنواع المصروفات</h1>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Add Expense Type Form -->
        <form action="manage_expense_types.php" method="POST">
            <label for="type">نوع المصروف:</label>
            <input type="text" name="type" id="type" required>
            <button type="submit" name="add_type">إضافة نوع المصروف</button>
        </form>

        <!-- Display All Expense Types -->
        <h2>جميع أنواع المصروفات</h2>
        <?php if (count($types) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>النوع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $type): ?>
                        <tr>
                            <td><?= htmlspecialchars($type['type_name']) ?></td>
                            <td>
                                <form action="manage_expense_types.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="type_id" value="<?= $type['id'] ?>">
                                    <button type="submit" name="delete_type" class="delete-btn">حذف</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>لا توجد أنواع مصروفات.</p>
        <?php endif; ?>
    </div>
</body>
</html>
