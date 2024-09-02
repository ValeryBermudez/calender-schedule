<?php
include 'db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Handle registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "User registered successfully.";
    } else {
        echo "Error registering user.";
    }
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Incorrect credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authentication</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .auth-container {
            display: flex;
            justify-content: space-between;
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .auth-form {
            width: 45%;
        }
        .auth-form h2 {
            text-align: center;
        }
        .auth-form form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-form">
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Log In</button>
            <?php if (isset($error)) echo '<p class="error">'.$error.'</p>'; ?>
        </form>
    </div>

    <div class="auth-form">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="nombre" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</div>

</body>
</html>
