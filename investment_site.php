<?php
session_start(); // بداية الجلسة

// إعدادات الاتصال بقاعدة البيانات (تأكد من تحديث هذه الإعدادات)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "investment_site";  // اسم قاعدة البيانات

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// تسجيل الدخول
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $email;  // حفظ بيانات المستخدم في الجلسة
        header("Location: dashboard.php");  // الانتقال إلى لوحة التحكم
    } else {
        echo "Invalid credentials!";
    }
}

// التسجيل
if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// الإيداع
if (isset($_POST['deposit'])) {
    $amount = $_POST['amount'];
    $sql = "INSERT INTO transactions (user_id, type, amount) VALUES ('{$_SESSION['user']}', 'deposit', '$amount')";
    if ($conn->query($sql) === TRUE) {
        echo "Deposit successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// السحب
if (isset($_POST['withdraw'])) {
    $amount = $_POST['amount'];
    $sql = "INSERT INTO transactions (user_id, type, amount) VALUES ('{$_SESSION['user']}', 'withdraw', '$amount')";
    if ($conn->query($sql) === TRUE) {
        echo "Withdrawal successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>

<!-- الصفحة الرئيسية -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Site</title>
</head>
<body>

    <!-- نموذج التسجيل -->
    <h2>Sign Up</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="register">Register</button>
    </form>

    <!-- نموذج تسجيل الدخول -->
    <h2>Login</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>

    <!-- نموذج الإيداع -->
    <?php if (isset($_SESSION['user'])): ?>
    <h2>Deposit</h2>
    <form method="post">
        <input type="number" name="amount" placeholder="Amount" required><br>
        <button type="submit" name="deposit">Deposit</button>
    </form>
    <?php endif; ?>

    <!-- نموذج السحب -->
    <?php if (isset($_SESSION['user'])): ?>
    <h2>Withdraw</h2>
    <form method="post">
        <input type="number" name="amount" placeholder="Amount" required><br>
        <button type="submit" name="withdraw">Withdraw</button>
    </form>
    <?php endif; ?>

</body>
</html>

<!-- لوحة التحكم -->
<?php
if (isset($_SESSION['user'])):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

    <h2>Welcome to your Dashboard, <?php echo $_SESSION['user']; ?></h2>

    <h3>Available Packages</h3>
    <ul>
        <li><a href="package.php?id=1">Package 1 - $100</a></li>
        <li><a href="package.php?id=2">Package 2 - $500</a></li>
        <li><a href="package.php?id=3">Package 3 - $1000</a></li>
    </ul>

    <h3>Your Transactions</h3>
    <table border="1">
        <tr>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
        <?php
        $sql = "SELECT * FROM transactions WHERE user_id='{$_SESSION['user']}'";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['type'] . "</td><td>" . $row['amount'] . "</td><td>" . $row['date'] . "</td></tr>";
        }
        ?>
    </table>

</body>
</html>
<?php endif; ?>
