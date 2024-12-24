<?php
$conn = new mysqli("localhost", "root", "", "supplier_system");

// Initialize variables for month and year, set to current if not provided
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Prepare the SQL query to fetch total expenses by type for the selected month and year
$query = "
    SELECT et.type_name, SUM(me.amount) AS total_expense
    FROM misc_expenses me
    INNER JOIN expense_types et ON me.type_id = et.id
    WHERE MONTH(me.expense_date) = ? AND YEAR(me.expense_date) = ?
    GROUP BY et.type_name
    ORDER BY total_expense DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

// Calculate the total expenses
$total_expenses = 0;
while ($row = $result->fetch_assoc()) {
    $total_expenses += $row['total_expense'];
}

// Move the result pointer back to the beginning for the second loop
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المصاريف الشهرية</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <a href="add_invoice.php">إضافة فاتورة</a>
        <a href="view_totals.php">حسابات الموردين</a>
        <a href="manage_suppliers.php">إدارة الموردين</a>
        <a href="search_invoices.php">بحث في الفواتير</a>
        <a href="manage_expense_types.php">إدارة المصروفات</a>
        <a href="view_monthly_expenses.php">المصاريف الشهرية</a>
    </header>

    <div class="container">
        <h1>المصاريف الشهرية لـ <?= $month ?>/<?= $year ?></h1>

        <!-- Month and Year Selection Form -->
        <form action="view_monthly_expenses.php" method="GET">
            <label for="month">الشهر:</label>
            <select name="month" id="month">
                <option value="1" <?= $month == '1' ? 'selected' : '' ?>>يناير</option>
                <option value="2" <?= $month == '2' ? 'selected' : '' ?>>فبراير</option>
                <option value="3" <?= $month == '3' ? 'selected' : '' ?>>مارس</option>
                <option value="4" <?= $month == '4' ? 'selected' : '' ?>>أبريل</option>
                <option value="5" <?= $month == '5' ? 'selected' : '' ?>>مايو</option>
                <option value="6" <?= $month == '6' ? 'selected' : '' ?>>يونيو</option>
                <option value="7" <?= $month == '7' ? 'selected' : '' ?>>يوليو</option>
                <option value="8" <?= $month == '8' ? 'selected' : '' ?>>أغسطس</option>
                <option value="9" <?= $month == '9' ? 'selected' : '' ?>>سبتمبر</option>
                <option value="10" <?= $month == '10' ? 'selected' : '' ?>>أكتوبر</option>
                <option value="11" <?= $month == '11' ? 'selected' : '' ?>>نوفمبر</option>
                <option value="12" <?= $month == '12' ? 'selected' : '' ?>>ديسمبر</option>
            </select>

            <label for="year">السنة:</label>
            <select name="year" id="year">
                <?php for ($i = date('Y'); $i >= 2000; $i--): ?>
                    <option value="<?= $i ?>" <?= $year == $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit">عرض المصاريف</button>
        </form>

        <!-- Display Total Expenses Per Type -->
        <table>
            <thead>
                <tr>
                    <th>نوع المصروف</th>
                    <th>إجمالي المصروف</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['type_name']) ?></td>
                        <td><?= number_format($row['total_expense'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>

                <!-- Total Row -->
                <tr class="total-row">
                    <td>إجمالي المصاريف</td>
                    <td><?= number_format($total_expenses, 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
