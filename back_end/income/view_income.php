<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../front_end/signin.html");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM Income WHERE user_id = ?");
$stmt->execute([$user_id]);
$incomes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Income</title>
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">Your Income</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Source</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php
                foreach ($incomes as $income): ?>
                    <tr>
                        <td><?= htmlspecialchars($income['income_source']) ?></td>
                        <td><?= number_format($income['income_amount'], 2) ?></td>

                        <td>
                            <a href="update_income.php?id=<?= $income['income_id'] ?>">Edit</a>
                            <a href="delete_income.php?id=<?= $income['income_id'] ?>" onclick="return confirm('Delete this income?');">Delete</a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Back to Home Button -->
        <div class="mt-4 text-center">
            <a href="../../front_end/home.html" class="btn btn-outline-secondary btn-lg">← Back to Home</a>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>