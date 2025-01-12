<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once("../database/database.php");
require_once("function.php");

if (!isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] !== true && $_SESION['PASSWORD_VERIFY'] !== true) {
    header('Location: ../index.php');
    exit();
}
function get_all_users() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users ");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function get_all_images() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM images");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
$category = isset($_GET['category']) ? $_GET['category'] : 'none';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icons/icons.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <title>Pinspire | Admin Panel</title>
</head>
<body>
<header class="mb-5">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" style="font-weight: bold; font-size: 30px; ">Pinspire</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse me-3" id="navbarcenter">
        <input type="text" name="search" id="search" class="form-control" placeholder="Поиск" style="width: 300px; margin-left: 20px;">
        </div>
        <div class="collapse navbar-collapse float-end" id="navbarNav">
            <ul class="navbar-nav me-5">
                <li class="nav-item ms-2 me-1">
                    <p class="mt-2 fw-bold"><?= htmlspecialchars($_SESSION['user']['Login'], ENT_QUOTES) ?></p>
                </li>
                <li class="nav-item mt-1 ms-3">
                    <img src="../<?= htmlspecialchars($_SESSION['user']['Avatar'], ENT_QUOTES) ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%;">
                </li>
                <li class="nav-item">
                <button class="btn btn-dark ms-3 me-0 mt-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><img src="../icons/menu.svg" style="width: 30px; height: 30px; object-fit: cover; filter: invert(1);"></button>
                </li>
            </ul>
        </div>
    </nav>
</header>
<hr>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
<div class="offcanvas-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <img src="../<?= htmlspecialchars($_SESSION['user']['Avatar'], ENT_QUOTES) ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 10px;">
        <p class="mt-2 fw-bold ms-auto"><?= htmlspecialchars($_SESSION['user']['Login'], ENT_QUOTES) ?>
        <?php if($_SESSION['admin_auth'] === true): ?>
            <span class="badge bg-danger">Admin</span>
        <?php endif; ?>
       </p>
    </div>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<hr>
<div class="offcanvas-body">
    <ul class="list-group">
            <li class="list-group-item">
                <a class="nav-link mb-1" href="../index.php">На главную</a>
            </li>
            <li class="list-group-item">
                <a class="nav-link mb-1" href="../profile/profile.php">Профиль</a>
            </li>
        </ul>
    </div>
    <a class="danger btn btn-danger" href="../auth/logout.php" style="border-radius: 0px;">Выход</a>
</div>
<main>
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
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Выбрать категорию
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="?category=users">Пользователи</a></li>
                    <li><a class="dropdown-item" href="?category=images">Картинки</a></li>
                    <?php if (isset($category) && $category != "none"): ?>
                    <li><a class="dropdown-item" href="?category=none">Сбросить выбор</a></li>
                    <?php endif; ?>
                </ul>
            </div>
<?php if ($category == "none"): ?>
            <h3 class="mt-3">Выберите категорию</h3>
<?php elseif ($category == "users"): ?>
<?php 
$all_users = get_all_users();
?>
            <h3>Пользователи</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Логин</th>
                        <th scope="col">Почта</th>
                        <th scope="col">Дата регистрации</th>
                        <th scope="col">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users as $users): ?>
                        <tr>
                            <th scope="row"><?= $users['ID'] ?></th>
                            <td><?= $users['Login'] ?></td>  
                            <td><?= $users['Email'] ?></td>
                            <td><?= $users['Date_reg'] ?></td>
                            <?php echo $users['is_active']; ?>
                            <td>
                                <?php if ($users['is_active'] == 'active') : ?>
                                    <span class="badge bg-success">Активен</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Забанен</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_user.php?id=<?= $users['ID'] ?>" class="btn btn-primary">Редактировать</a>
                                <form action="" method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $users['ID'] ?>">
                                    <input type="hidden" name="action" value="ban_user">
                                    <button class="btn btn-danger">Забанить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
<?php elseif($category == "images"): ?>
<?php $images = get_all_images(); ?>
            <h3>Картинки</h3>
            <?php
            $images_on_page = 10;
            $page = (int)(isset($_GET['page']) ? $_GET['page'] : 1);
            $start = ($page - 1) * $images_on_page;
            $images_count = count($images);
            $pages_count = ceil($images_count / $images_on_page);
            $images = array_slice($images, $start, $images_on_page);
            ?>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?category=<?= $category ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $pages_count; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?category=<?= $category ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $pages_count): ?>
                        <li class="page-item">
                            <a class="page-link" href="?category=<?= $category ?>&page=<?= $page + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Картинка</th>
                        <th scope="col">Название</th>
                        <th scope="col">Дата загрузки</th>
                        <th scope="col">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($images as $image): ?>
                        <tr>
                            <th scope="row"><?= $image['ID'] ?></th>
                            <th scope="row"><img src="../<?= $image['Path'] ?>" alt="" style="width: 100px; height: 100px; object-fit: cover;"></th>
                            <td><?= $image['Image_Name'] ?></td>  
                            <td><?= $image['Date_upload'] ?></td>
                            <td>
                                <a href="redact.php?id=<?= $image['ID'] ?>" class="btn btn-primary">Редактировать</a>
                                <a href="?id=<?= $image['ID'] ?>" class="btn btn-danger">Забанить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
<?php endif; ?>         
        </div>
    </div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>