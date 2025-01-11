<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('../database/database.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['login']), ENT_QUOTES, 'UTF-8');
    $pass = trim($_POST['password']);
    $stmt = $conn->prepare("SELECT ID, Password, is_admin FROM users WHERE Login = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($id, $hashedPassword, $is_admin);

    if ($stmt->fetch()) {
        if (password_verify($pass, $hashedPassword)) {
            $_SESSION['auth'] = true;
            $_SESSION['user_login'] = $name;
            $_SESSION['user_id'] = $id;

            if ($is_admin === 'admin') {
                $_SESSION['admin_auth'] = true;
                $_SESSION['system_admin'] = false;
                header('Location: ../index.php');
                exit();
            } elseif ($is_admin === 'user') {
                $_SESSION['admin_auth'] = false;
                $_SESSION['system_admin'] = false;
                header('Location: ../index.php');
                exit();
            } elseif ($is_admin === 'system_admin') {
                $_SESSION['admin_auth'] = true;
                $_SESSION['system_admin'] = true;
                header('Location: ../index.php');
                exit();
            }
        } else {
            $_SESSION['error'] = '<div class="alert alert-danger" role="alert">Неверный пароль</div>';
            header('Location: signin.php');
            exit();
        }
    } else {
        $_SESSION['error'] = '<div class="alert alert-danger" role="alert">Пользователь не найден</div>';
        header('Location: signin.php');
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
    <title>Регистрация</title>
</head>
<body>
    <div class="container mt-5 d-flex justify-content-center">
        <div class="col-md-5">    
            <?php if (isset($_SESSION['error'])): ?>
            <?= $_SESSION['error'];?>
            <?php endif; ?>
            <h1 class="text-center">Авторизация</h1>
            <form method="post" action class="border p-4">
                <div class="form-group">
                    <label for="name">Логин</label>
                    <input type="text" name="login" id="name" class="form-control" required>
                </div>
                <div class="form-group mt-3">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-4">Войти</button>
                <p class="text-start mt-1"> Еще нет аккаунта? <a href="signup.php" class="text-end">Заргегестрируйтесь</a></p>
            </form>
        </div>
    </div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>
</body>
</html>