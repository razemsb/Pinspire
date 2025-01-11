<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('../database/database.php');

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validate_email($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['login']);
    $pass = sanitize_input($_POST['password']);
    $repeatpass = sanitize_input($_POST['repeatpassword']);
    $email = validate_email($_POST['email']);

    if (empty($name) || empty($pass) || empty($repeatpass) || empty($email)) {
        echo "<script>alert('Все поля обязательны для заполнения.'); window.history.back();</script>";
        exit();
    }

    if ($pass !== $repeatpass) {
        echo "<script>alert('Пароли не совпадают.'); window.history.back();</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Некорректный email.'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT Login, Email FROM users WHERE Login = ? OR Email = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param("ss", $name, $email);
    if (!$stmt->execute()) {
        die("Ошибка выполнения запроса: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('Логин или email уже занят.'); window.history.back();</script>";
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

    $is_admin = 'user';
    $avatar = 'uploads/basic_avatar.svg'; 

    $stmt = $conn->prepare("INSERT INTO users (Login, Password, Email, Date_reg, Avatar, is_admin) VALUES (?, ?, ?, NOW(), ?, ?)");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $hashed_password, $email, $avatar, $is_admin);
    if (!$stmt->execute()) {
        die("Ошибка выполнения запроса: " . $stmt->error);
    }

    $_SESSION['auth'] = true;
    if ($is_admin === 'admin') {
        $_SESSION['admin_auth'] = true;
        $_SESSION['system_admin'] = false;
    } elseif ($is_admin === 'user') {
        $_SESSION['admin_auth'] = false;
        $_SESSION['system_admin'] = false;
    } elseif ($is_admin === 'system_admin') {
        $_SESSION['admin_auth'] = true;
        $_SESSION['system_admin'] = true;
    }
    $_SESSION['user_id'] = $conn->insert_id;

    session_regenerate_id(true);

    echo "<script>alert('Регистрация прошла успешно!'); window.location.href = '../index.php';</script>";

    $stmt->close();
    $conn->close();
}
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
            <h1 class="text-center">Регистрация</h1>
            <form method="post" action class="border p-4">
                <div class="form-group">
                    <label for="name">Логин</label>
                    <input type="text" name="login" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    <div class="invalid-feedback" id="password-feedback"></div>
                </div>
                <div class="form-group">
                    <label for="repeatpassword">Повторите пароль</label>
                    <input type="password" name="repeatpassword" id="repeatpassword" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Зарегистрироваться</button>
                <p class="text-start mt-1"> Уже есть аккаунт? <a href="signin.php" class="text-end">Войти</a></p>
            </form>
        </div>
    </div>
<script>
    const passwordInput = document.getElementById('password');
    const feedback = document.getElementById('password-feedback');
    passwordInput.addEventListener('input', (e) => {
        if (e.target.value.length < 8) {
            feedback.innerText = 'Минимальная длина пароля - 8 символов';
            feedback.style.display = 'block';
        } else {
            feedback.style.display = 'none';
        }
    });
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>
</body>
</html>