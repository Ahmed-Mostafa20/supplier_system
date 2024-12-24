<?php
session_start();
$conn = new mysqli("localhost", "root", "", "supplier_system");

// Initialize variables
$suppliers_result = $conn->query("SELECT * FROM suppliers");
$expense_types_result = $conn->query("SELECT * FROM expense_types");
$message = '';
$data = [];
$supplier_totals = []; // Initialize the supplier totals array

// Handle setting the constant date
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_date'])) {
    $_SESSION['selected_date'] = $_POST['selected_date'];
    $message = "تم تعيين التاريخ إلى " . $_POST['selected_date'];
}

// Get the current constant date or default to today
$constant_date = $_SESSION['selected_date'] ?? date('Y-m-d');

// Handle adding an invoice
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_invoice'])) {
    $supplier_id = $_POST['supplier_id'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $note = $_POST['note'];

    if ($supplier_id && $amount && $type) {
        $stmt = $conn->prepare("INSERT INTO invoices (supplier_id, amount, type, invoice_date, note) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $supplier_id, $amount, $type, $constant_date, $note);
        $stmt->execute();
        $message = "تم إضافة الفاتورة بنجاح للتاريخ $constant_date.";

        // Redirect to prevent re-submission on refresh
        header("Location: add_invoice.php");
        exit();
    } else {
        $message = "يرجى ملء جميع الحقول.";
    }
}

// Handle adding a miscellaneous expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $type_id = $_POST['type_id'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];

    if ($type_id && $amount && $expense_date) {
        $stmt = $conn->prepare("INSERT INTO misc_expenses (type_id, amount, expense_date) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $type_id, $amount, $expense_date);
        $stmt->execute();
        $message = "تم إضافة المصروف بنجاح للتاريخ $expense_date.";
    } else {
        $message = "يرجى ملء جميع الحقول.";
    }
}

// Fetch all invoices and expenses for the current date
$stmt = $conn->prepare(
    "SELECT i.id AS invoice_id, s.name AS supplier_name, i.amount, i.type AS invoice_type, i.invoice_date, i.note, NULL AS expense_id, NULL AS expense_type, NULL AS expense_amount, NULL AS expense_date 
    FROM invoices i 
    INNER JOIN suppliers s ON i.supplier_id = s.id 
    WHERE i.invoice_date = ? 
    UNION ALL 
    SELECT NULL AS invoice_id, NULL AS supplier_name, e.amount, NULL AS invoice_type, e.expense_date, NULL AS note, e.id AS expense_id, t.type_name AS expense_type, e.amount AS expense_amount, e.expense_date 
    FROM misc_expenses e 
    INNER JOIN expense_types t ON e.type_id = t.id 
    WHERE e.expense_date = ?"
);
$stmt->bind_param("ss", $constant_date, $constant_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    
    // Track total amount for each supplier (only for invoices)
    if ($row['invoice_id']) {
        if (!isset($supplier_totals[$row['supplier_name']])) {
            $supplier_totals[$row['supplier_name']] = 0;
        }

        // Update supplier total based on invoice type
        if ($row['invoice_type'] === 'اجل') {
            $supplier_totals[$row['supplier_name']] += $row['amount'];
        } elseif ($row['invoice_type'] === 'تحصيل') {
            $supplier_totals[$row['supplier_name']] -= $row['amount'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة فاتورة ومصروف</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            direction: rtl;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #006f6b;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        header a {
            color: white;
            font-size: 18px;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
        }

        header a:hover {
            background-color: #005e57;
        }

        header img {
            height: 50px;
        }

        .container {
            padding: 30px;
            background-color: white;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #006f6b;
        }

        form {
            margin-bottom: 20px;
        }

        label, select, input, textarea {
            display: block;
            width: 100%;
            margin-bottom: 12px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        label {
            font-weight: bold;
        }

        input[type="number"], input[type="date"] {
            width: 48%;
            display: inline-block;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        td {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 30px;
        }
        .header-right {

            display: flex;
            align-items: center;
        }
        .header-right form {
            margin-top: 20px;
            display: block;
            margin-right: 850px;
            align-items: center;
        }
        .header-right input[type="date"], .header-right button {
    width: 100%;
    margin-bottom: 10px; /* Adds spacing between the input and the button */
}
   .invoice-form {
       width: 40%;
   }     
.misc-form {
    width: 40%;
    margin: auto;
}
.big-form {
    display: flex;
}
    </style>
</head>
<body>
   <!-- Navigation Bar -->
   <header>
        <a href="add_invoice.php">إضافة فاتورة</a>
        <a href="view_totals.php"> حسابات الموردين</a>
        <a href="manage_suppliers.php">إدارة الموردين</a>
        <a href="search_invoices.php">بحث في الفواتير</a>
        <a href="manage_expense_types.php">إدارة  المصروفات</a>
        <a href="view_monthly_expenses.php">المصاريف الشهرية</a>
    </header>
    <div class="header-right">
            <img src="logo.png" alt="Logo" height="200" width="250"> <!-- Logo placeholder -->
            <!-- Set Constant Date Form -->
             
            <form action="add_invoice.php" method="POST" >
                <label for="selected_date">تعيين تاريخ الفاتورة:</label>
                <input type="date" name="selected_date" id="selected_date" value="<?= htmlspecialchars($constant_date) ?>" required>
                <button type="submit" name="set_date">تعيين التاريخ</button>
            </form>
        </div>          
    <div class="container">
        <h1>إضافة فاتورة و نثريات</h1>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <h2>التاريخ الحالي للفواتير والمصروفات: <?= htmlspecialchars($constant_date) ?></h2>

        <!-- Set Constant Date -->
        
        <!-- Add Invoice Form -->
         <div class="big-form">
        <form class="invoice-form" action="add_invoice.php" method="POST">
        <h2>إضافة فاتورة</h2>

            <select name="supplier_id" id="supplier_id" required>
                <option value="">-- اختر المورد --</option>
                <?php while ($supplier = $suppliers_result->fetch_assoc()): ?>
                    <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="amount">مبلغ الفاتورة:</label>
            <input type="number" name="amount" id="amount" required step="0.01">

            <label for="type">نوع الفاتورة:</label>
            <select name="type" id="type" required>
                <option value="اجل">اجل </option>
                <option value="تحصيل">تحصيل</option>
                <option value="كاش">كاش  </option>
            </select>

            <label for="note">ملاحظة:</label>
            <textarea name="note" id="note" placeholder="أدخل أي تفاصيل إضافية"></textarea>

            <button type="submit" name="add_invoice">إضافة الفاتورة</button>
        </form>

        <!-- Add Miscellaneous Expense Form -->
        <form class="misc-form" action="add_invoice.php" method="POST">
        <h2>إضافة نثريات </h2 >

            <select name="type_id" id="type_id" required>
                <option value="">-- اختر نوع النثرية --</option>
                <?php while ($type = $expense_types_result->fetch_assoc()): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['type_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="amount">المبلغ:</label>
            <input type="number" name="amount" id="amount" required step="0.01">

            <button type="submit" name="add_expense">إضافة المصروف</button>
        </form>
</div>
        <!-- Display Invoices for the Selected Date -->
        <h2>الفواتير لـ <?= htmlspecialchars($constant_date) ?></h2>
        <?php if (count($data) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>المورد</th>
                        <th>المبلغ</th>
                        <th>النوع</th>
                        <th>التاريخ</th>
                        <th>الملاحظة</th>
                        <th>الإجمالي بعد الفاتورة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $supplier_totals_display = [];

                    foreach ($data as $row):
                        if ($row['invoice_id']):
                            if (!isset($supplier_totals_display[$row['supplier_name']])) {
                                $supplier_totals_display[$row['supplier_name']] = 0;
                            }

                            if ($row['invoice_type'] === 'اجل') {
                                $supplier_totals_display[$row['supplier_name']] += $row['amount'];
                            } elseif ($row['invoice_type'] === 'تحصيل') {
                                $supplier_totals_display[$row['supplier_name']] -= $row['amount'];
                            }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                            <td><?= number_format($row['amount'], 2) ?> EGP</td>
                            <td><?= htmlspecialchars($row['invoice_type']) ?></td>
                            <td><?= htmlspecialchars($row['invoice_date']) ?></td>
                            <td><?= htmlspecialchars($row['note']) ?></td>
                            <td>
                                <?= isset($supplier_totals_display[$row['supplier_name']]) ? number_format($supplier_totals_display[$row['supplier_name']], 2) : '0.00' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>لا توجد فواتير مضافة لهذا التاريخ.</p>
        <?php endif; ?>

        <!-- Display Expenses for the Selected Date -->
        <h2>النثريات المتنوعة لـ <?= htmlspecialchars($constant_date) ?></h2>
        <?php if (count($data) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>نوع النثرية</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <?php if ($row['expense_id']): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['expense_type']) ?></td>
                                <td><?= number_format($row['expense_amount'], 2) ?> EGP</td>
                                <td><?= htmlspecialchars($row['expense_date']) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>لا توجد مصروفات مضافة لهذا التاريخ.</p>
        <?php endif; ?>
    </div>
</body>
</html>
