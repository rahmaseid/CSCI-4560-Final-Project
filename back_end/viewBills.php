<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login to view your bills.");
}

// Database connection
$conn = new mysqli('mysql', 'root', 'root', 'budgeting_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bill data for the selected month
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bill_id'])) {
    $billId = $_POST['bill_id'];

    // Check if the action is to mark the bill as paid or undo the payment
    if (isset($_POST['mark_paid'])) {
        // Mark bill as paid
        $updateStmt = $conn->prepare("UPDATE Bills SET payment_status = 1, payment_date = CURDATE() WHERE bill_id = ? AND user_id = ?");
        $updateStmt->bind_param("ii", $billId, $user_id);
        $updateStmt->execute();
        $updateStmt->close();
    } elseif (isset($_POST['undo_payment'])) {
        // Undo the payment (set to unpaid)
        $updateStmt = $conn->prepare("UPDATE Bills SET payment_status = 0, payment_date = NULL WHERE bill_id = ? AND user_id = ?");
        $updateStmt->bind_param("ii", $billId, $user_id);
        $updateStmt->execute();
        $updateStmt->close();
    }
}

// Get filters
$month = $_GET['month'] ?? 'all';
$year = $_GET['year'] ?? 'all';

// Build dynamic WHERE clause
$where = "WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($month !== 'all') {
    $where .= " AND MONTH(due_date) = ?";
    $params[] = intval($month);
    $types .= "i";
}

if ($year !== 'all') {
    $where .= " AND YEAR(due_date) = ?";
    $params[] = intval($year);
    $types .= "i";
}

// Prepare query
$sql = "SELECT bill_id, bill_name, bill_amount, due_date, payment_date, payment_status 
        FROM Bills 
        $where 
        ORDER BY due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title> Bills List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">
  <h2 class="mb-4">Your Bills List</h2>

  <?php if ($result->num_rows > 0): ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Bill Name</th>
          <th>Due Date</th>
          <th>Payment Date</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['bill_name']) ?></td>
            <td><?= date("F j, Y", strtotime($row['due_date'])) ?></td>
            <td>
            <?= $row['payment_date'] ? date("F j, Y", strtotime($row['payment_date'])) : 'N/A' ?>
            </td>
            <td>$<?= number_format($row['bill_amount'], 2) ?></td>
            <td>
            <?= $row['payment_status'] ? '<span class="text-success">Paid</span>' : '<span class="text-danger">Unpaid</span>' ?>
            </td>
            <td>
            <?php if (!$row['payment_status']): ?>
                <form method="post" action="" class="d-inline">
                <input type="hidden" name="bill_id" value="<?= $row['bill_id'] ?>">
                <button type="submit" name="mark_paid" class="btn btn-sm btn-success">Mark as Paid</button>
                </form>
            <?php else: ?>
                <form method="post" action="" class="d-inline">
                <input type="hidden" name="bill_id" value="<?= $row['bill_id'] ?>">
                <button type="submit" name="undo_payment" class="btn btn-sm btn-warning">Undo Payment</button>
                </form>
            <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>

      </tbody>
    </table>
  <?php else: ?>
    <p>No bills found for the selected month and year.</p>
  <?php endif; ?>

  <!-- Centered Button -->
  <div class="text-center mt-4">
     <a href='../front_end/home.html' class="btn btn-success">Go Back to Dashboard</a>
  </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
