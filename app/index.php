<?php
session_start();
require "db.php";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$_POST['username'], $_POST['password']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    }
    $error = "Invalid login";
}
?>
<!DOCTYPE html>
<html>
<head><title>IDOR Lab</title></head>
<body>
<h1>IDOR Lab (intentionally vulnerable)</h1>

<?php if (!isset($_SESSION['user_id'])): ?>
    <form method="POST">
        <input name="username" placeholder="Username">
        <input name="password" type="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>

<?php else: ?>
    <p>Logged in as <?= htmlspecialchars($_SESSION['username']) ?> | <a href="?logout=1">Logout</a></p>

    <?php if (isset($_GET['invoice'])): ?>
        <?php
        // VULNERABLE: no check that this invoice belongs to the logged-in user
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['invoice'], $_SESSION['user_id']]);
        $inv = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <?php if ($inv): ?>
            <h2><?= htmlspecialchars($inv['title']) ?></h2>
            <p>Amount: $<?= htmlspecialchars($inv['amount']) ?></p>
            <p>Notes: <?= htmlspecialchars($inv['notes']) ?></p>
        <?php else: ?>
            <p>Invoice not found.</p>
        <?php endif; ?>
        <p><a href="index.php">Back</a></p>

    <?php else: ?>
        <h2>My invoices</h2>
        <?php
        $stmt = $pdo->prepare("SELECT id, title FROM invoices WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        foreach ($stmt as $row): ?>
            <p><a href="?invoice=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></p>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>