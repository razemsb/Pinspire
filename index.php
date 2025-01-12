<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set('Europe/Moscow');
require_once ("database/database.php");
if ($_SESSION['auth'] == false) {
    header("Location: auth/signin.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$_SESSION['user'] = $user;
$stmt->close();

$stmt2 = $conn->prepare("SELECT * FROM images WHERE Active = 'active'");
$stmt2->execute();
$result2 = $stmt2->get_result(); 
$images = [];
if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $images[] = $row;
    }
}
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icons/icons.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
    <title>Pinspire | Gallery</title>
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
        <a href="profile/profile.php"><img src="icons/upload.svg" style="width: 30px; height: 30px; object-fit: cover;"></a>
        <div class="collapse navbar-collapse me-3" id="navbarcenter">
        <input type="text" name="search" id="search" class="form-control" placeholder="Поиск" style="width: 300px; margin-left: 20px;">
        </div>
        <div class="collapse navbar-collapse float-end" id="navbarNav">
            <ul class="navbar-nav me-5">
                <li class="nav-item ms-2 me-1 mt-2">
                    <a href="profile/profile.php" class="mt-2 fw-bold" style="text-decoration: none; color: black;"><?= htmlspecialchars($_SESSION['user']['Login'], ENT_QUOTES) ?></a>
                </li>
                <li class="nav-item mt-1 ms-3">
                    <img src="<?= htmlspecialchars($_SESSION['user']['Avatar'], ENT_QUOTES) ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%;">
                </li>
                <li class="nav-item">
                <button class="btn btn-dark ms-3 me-0 mt-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><img src="icons/menu.svg" style="width: 30px; height: 30px; object-fit: cover; filter: invert(1);"></button>
                </li>
            </ul>
        </div>
    </nav>
</header>
<hr>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
<div class="offcanvas-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <img src="<?= htmlspecialchars($_SESSION['user']['Avatar'], ENT_QUOTES) ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 10px;">
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
            <?php if($_SESSION['admin_auth'] === true): ?>
            <li class="list-group-item admin">
                <a class="nav-link mb-1" href="admin/index.php">Админ панель</a>
            </li>
            <?php endif; ?>
            <li class="list-group-item">
                <a class="nav-link mb-1" href="profile/profile.php">Профиль</a>
            </li>
        </ul>
    </div>
    <a class="danger btn btn-danger" href="auth/logout.php" style="border-radius: 0px;">Выход</a>
</div>
<main class="pt-5">
    <div class="row contents-image mt-3 container-from-image">
    <?php foreach ($images as $image) { ?>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="card mt-3 mb-3 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                <img src="<?= htmlspecialchars($image['Preview_Path'], ENT_QUOTES) ?>" class="card-img-top" alt="Изображение" style="width: 100%;">
                  <div class="card-body d-flex flex-column">
                      <h6 class="card-title text-truncate" style="font-weight: bold;">
                          <?= htmlspecialchars(strlen($image['Image_Name']) > 20 ? substr($image['Image_Name'], 0, 20) . '...' : $image['Image_Name'], ENT_QUOTES) ?>
                      </h6>
                      <p class="text-muted text-truncate mb-2">
                          <?= htmlspecialchars(strlen($image['Description']) > 50 ? substr($image['Description'], 0, 50) . '...' : $image['Description'], ENT_QUOTES) ?>
                      </p>
                      <p class="mb-2" style="font-size: 14px; color: #555;">
                          <strong>Автор:</strong> 
                          <?php
                          $stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
                          $stmt->bind_param("i", $image['upload_user_id']);
                          $stmt->execute();
                          $upload_user = $stmt->get_result()->fetch_assoc();
                          echo htmlspecialchars($upload_user['Login'], ENT_QUOTES);
                          ?>
                      </p>
                      <div class="d-flex justify-content-between align-items-center mt-auto">
                          <a href="#" type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $image['ID'] ?>">Подробнее</a>
                          <span class="badge bg-secondary" style="font-size: 12px;"><?= htmlspecialchars($image['Category'], ENT_QUOTES) ?></span>
                      </div>
                  </div>
              </div>
          </div>
    <?php } ?>
    <?php if (empty($images)) { ?>
        <div class="col-md-12 text-center">
            <h2>Изображений нет</h2>
        </div>
    <?php } ?>
    </div>
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
                <img src="<?= htmlspecialchars($image['Path'], ENT_QUOTES) ?>" class="img-fluid" alt="..." style="max-width: 100%; max-height: 500px;">
                <p class="mt-2"><?= htmlspecialchars(date('d.m.Y в H:i', strtotime($image['Date_upload'])), ENT_QUOTES) ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>