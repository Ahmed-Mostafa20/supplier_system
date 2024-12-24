<?php
$conn = new mysqli("localhost", "root", "", "supplier_system");

// Check if the invoice ID is provided
if (!isset($_GET['id'])) {
    die("Invoice ID is required.");
}

$invoice_id = $_GET['id'];

// Fetch the invoice details
$query = "SELECT * FROM invoices WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

// Handle the form submission to update the invoice
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $invoice_date = $_POST['invoice_date'];

    // Update the invoice in the database
    $update_query = "UPDATE invoices SET amount = ?, type = ?, invoice_date = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("dssi", $amount, $type, $invoice_date, $invoice_id);
    $stmt->execute();

    // Redirect back to the search page after updating
    header("Location: search_invoices.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 20px; }
        form { max-width: 500px; margin: auto; }
        label, input, select { display: block; width: 100%; margin-bottom: 10px; }
        button { padding: 10px; background: #5cb85c; color: white; border: none; cursor: pointer; }
        button:hover { background: #4cae4c; }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Invoice</h1>
    <form action="edit_invoice.php?id=<?= $invoice['id'] ?>" method="POST">
        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" value="<?= htmlspecialchars($invoice['amount']) ?>" required>

        <label for="type">Type:</label>
        <select name="type" id="type" required>
            <option value="اجل" <?= $invoice['type'] === 'اجل' ? 'selected' : '' ?>>اجل</option>
            <option value="تحصيل" <?= $invoice['type'] === 'تحصيل' ? 'selected' : '' ?>>تحصيل</option>
            <option value="كاش" <?= $invoice['type'] === 'كاش' ? 'selected' : '' ?>>كاش</option>
        </select>

        <label for="invoice_date">Invoice Date:</label>
        <input type="date" name="invoice_date" id="invoice_date" value="<?= htmlspecialchars($invoice['invoice_date']) ?>" required>

        <button type="submit">Update Invoice</button>
    </form>
</div>

</body>
</html>
