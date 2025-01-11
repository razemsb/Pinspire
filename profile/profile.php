<?php
session_start();
require_once("../database/database.php");
$stmt = $conn->prepare("SELECT * FROM images WHERE upload_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$images = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icons/icons.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <title>Pinspire | <?= $_SESSION['user']['Login'] ?></title>
</head>
<body>
<header class="mb-5">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" style="font-weight: bold; font-size: 30px;" href="../index.php">Pinspire</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
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
            <?php if($_SESSION['admin_auth'] === true): ?>
            <li class="list-group-item admin">
                <a class="nav-link mb-1" href="">Админ панель</a>
            </li>
            <?php endif; ?>
            <li class="list-group-item">
                <a class="nav-link mb-1" href="">Загрузить изображение</a>
            </li>
        </ul>
    </div>
    <a class="danger btn btn-danger" href="../auth/logout.php" style="border-radius: 0px;">Выход</a>
</div>
<main>
<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-12 border shadow p-4">
            <h1>Профиль</h1>
            <div class="d-flex justify-content-center">
            <img src="../<?= htmlspecialchars($_SESSION['user']['Avatar'], ENT_QUOTES) ?>" alt="..." class="rounded-circle" style="width: 200px; height: 200px;">
            </div>
            <p class="text-muted border-bottom mt-3" style="font-size: 20px;">Логин: <?= htmlspecialchars($_SESSION['user']['Login'], ENT_QUOTES) ?></p>
            <p class="text-muted border-bottom mt-3" style="font-size: 20px;">Почта: <?= htmlspecialchars($_SESSION['user']['Email'], ENT_QUOTES) ?></p>
            <p class="text-muted border-bottom mt-3" style="font-size: 20px;">Колличество фото: <?= htmlspecialchars(count($images), ENT_QUOTES) ?></p>
            <p class="text-muted border-bottom mt-3" style="font-size: 20px;">Дата регистрации: <?= htmlspecialchars(date('d.m.Y в H:i', strtotime($_SESSION['user']['Date_reg'])), ENT_QUOTES) ?></p>
        </div>
    </div>
    <div class="row">
        <div class="row mt-3 d-flex" style="margin-left: auto; margin-right: auto">
            <div class="d-flex justify-content-end">
                <button class="btn btn-outline-dark me-2" style="width: 160px;" id="showAll">Показать все</button>
                <button class="btn btn-outline-dark me-2" style="display: none; width: 160px;" id="hideAll">Скрыть все</button>
            </div>
            <h2 class="text-center mt-2">Ваши картинки (<?= count($images) ?>)</h2>
            <?php
            if (empty($images)) {
                echo '<div class="col-12 text-center border shadow p-4"><h2>Вы не опубликовали ни одной картинки</h2></div>';
            } else {
                echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4" id="images">';
                 foreach ($images as $image) {  ?>
                    <div class="col">
                        <div class="card mt-2 mb-2" style="display: none;">
                            <img src="../<?= htmlspecialchars($image['Path'], ENT_QUOTES) ?>" class="card-img-top" alt="..." style="height: 350px;">
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars(strlen($image['Image_Name']) > 15 ? substr($image['Image_Name'], 0, 20) . '...' : $image['Image_Name'], ENT_QUOTES) ?></h6>
                                <p class="text-muted"><?= htmlspecialchars(strlen($image['Description']) > 25 ? substr($image['Description'], 0, 20) . '...' : $image['Description'], ENT_QUOTES) ?></p>
                                <p>Автор: <?php
                                $stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
                                $stmt->bind_param("i", $image['upload_user_id']);
                                $stmt->execute();
                                $upload_user = $stmt->get_result()->fetch_assoc();
                                echo htmlspecialchars($upload_user['Login'], ENT_QUOTES);
                                ?></p>
                                 <div class="d-flex justify-content-between align-items-center">
                                 <a href="#" type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $image['ID'] ?>">Подробнее</a>
                                    <p class="mb-0 text-end text-muted" style="margin-left: auto; font-size: 15px"><?= htmlspecialchars($image['Category'], ENT_QUOTES) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                 } 
            }
            ?>
            </div>
        </div>
    </div>
</div>
</main>
<script src="../scripts/view.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
<?php foreach ($images as $image) : ?>
<div class="modal fade" id="exampleModal<?= $image['ID'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?= $image['ID'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel<?= $image['ID'] ?>"><?= htmlspecialchars($image['Image_Name'], ENT_QUOTES) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class=""><?= htmlspecialchars($image['Description'], ENT_QUOTES) ?></p>
                <p class="">Автор: <?php
                $stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
                $stmt->bind_param("i", $image['upload_user_id']);
                $stmt->execute();
                $upload_user = $stmt->get_result()->fetch_assoc();
                echo htmlspecialchars($upload_user['Login'], ENT_QUOTES);
                ?></p>
                <img src="../<?= htmlspecialchars($image['Path'], ENT_QUOTES) ?>" class="img-fluid" alt="..." style="max-width: 100%; max-height: 500px;">
                <p class="mt-2"><?= htmlspecialchars(date('d.m.Y в H:i', strtotime($image['Date_upload'])), ENT_QUOTES) ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>