<?php
session_start();
require_once("../database/database.php");
if($_SESSION['PASSWORD_VERIFY'] === true) {
    header('Location: admin_panel.php');
    exit();
}
if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = htmlspecialchars(trim($_POST['login']), ENT_QUOTES, 'UTF-8');
    $pass = trim($_POST['password']);
    $stmt = $conn->prepare("SELECT Admin_ID, Admin_password FROM admin WHERE Admin_login = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    $stmt->bind_param("s", $_SESSION['user']['Login']);
    $stmt->execute();
    $stmt->bind_result($id, $hashedPassword);
    if ($stmt->fetch()) {
        if (password_verify($pass, $hashedPassword)) {
            $_SESION['PASSWORD_VERIFY'] = True;
            $_SESSION['admin_auth'] = true;
            header('Location: admin_panel.php');
            exit();
        } else {
            $_SESSION['error'] = "Неверные данные";
        }
    } else {
        $_SESSION['error'] = "Пользователь не найден";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icons/icons.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
    <title>Pinspire | Admin Login</title>
</head>
<body>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-12 border shadow p-4">
                <div>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <h1>Админ панель</h1>
                <form action="" method="post" class="mt-3">
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <input type="text" name="login" class="form-control" id="login" placeholder="Login">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-outline-dark">Войти</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>