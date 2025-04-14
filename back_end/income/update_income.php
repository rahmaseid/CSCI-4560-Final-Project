<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$income_id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $source = trim($_POST['income_source']);
    $amount = floatval($_POST['income_amount']);

    $stmt = $pdo->prepare("UPDATE Income SET income_source = ?, income_amount = ? WHERE income_id = ? AND user_id = ?");
    $stmt->execute([$source, $amount, $income_id, $user_id]);

    header("Location: view_income.php");
    exit;
}

//load current income data
$stmt = $pdo->prepare("SELECT * FROM Income WHERE income_id = ? AND user_id = ?");
$stmt->execute([$income_id, $user_id]);
$income = $stmt->fetch();

if (!$income) {
    die("Income record not found");
}

?>

<form method="POST">
    <label>Source:
        <input type="text" name="income_source" value="<?= htmlspecialchars($income['income_source']) ?>" required>
    </label><br>
    <label>Amount:
        <input type="number" name="income_amount" step="0.01" value="<?= $income['income_amount'] ?>" required>
    </label>
    <button type="submit">Update Income</button>
</form>