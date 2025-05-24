<?php
session_start();

// İstifadəçi adı və şifrə (sən dəyişə bilərsən)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '1234');

// Balans faylı
define('BALANCE_FILE', 'balances.json');

// Admindən çıxış funksiyası
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Giriş yoxlaması
if (!isset($_SESSION['admin'])) {
    // Giriş formu göndərilibsə
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';
        if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
            $_SESSION['admin'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "İstifadəçi adı və ya şifrə yalnışdır.";
        }
    }

    // Login formu
    ?>
    <!DOCTYPE html>
    <html lang="az">
    <head><meta charset="UTF-8"><title>Admin Login</title></head>
    <body>
    <h2>Admin Panel Girişi</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        İstifadəçi adı: <input type="text" name="username" required><br><br>
        Şifrə: <input type="password" name="password" required><br><br>
        <button type="submit">Daxil ol</button>
    </form>
    </body>
    </html>
    <?php
    exit;
}

// Admin panel

// Balansları oxu
function readBalances() {
    if (!file_exists(BALANCE_FILE)) {
        file_put_contents(BALANCE_FILE, json_encode(new stdClass()));
    }
    $json = file_get_contents(BALANCE_FILE);
    return json_decode($json, true) ?? [];
}

// Balansları yaz
function writeBalances($balances) {
    file_put_contents(BALANCE_FILE, json_encode($balances, JSON_PRETTY_PRINT));
}

// Balansları yenilə
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user']) && isset($_POST['balance'])) {
    $user = trim($_POST['user']);
    $balance = floatval($_POST['balance']);

    $balances = readBalances();
    $balances[$user] = $balance;
    writeBalances($balances);
    $message = "Balans yeniləndi.";
}

$balances = readBalances();

?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Balansların idarəsi</title>
</head>
<body>
<h1>Admin Panel - Balansların idarəsi</h1>

<p><a href="admin.php?logout=1">Çıxış</a></p>

<?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

<h2>Balanslar</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>İstifadəçi</th>
        <th>Balans</th>
        <th>Yenilə</th>
    </tr>
    <?php foreach ($balances as $user => $balance): ?>
        <tr>
            <form method="post">
                <td><input type="text" name="user" value="<?php echo htmlspecialchars($user); ?>" readonly></td>
                <td><input type="number" step="0.01" name="balance" value="<?php echo htmlspecialchars($balance); ?>" required></td>
                <td><button type="submit">Yenilə</button></td>
            </form>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Yeni istifadəçi əlavə et</h3>
<form method="post">
    İstifadəçi adı: <input type="text" name="user" required>
    Balans: <input type="number" step="0.01" name="balance" required>
    <button type="submit">Əlavə et</button>
</form>

</body>
</html>
