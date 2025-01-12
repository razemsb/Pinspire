<?php 
session_start();
require_once("../database/database.php");
require_once("function.php");
if (!isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] !== true && $_SESION['PASSWORD_VERIFY'] !== true) {
    header('Location: ../index.php');
    exit();
}
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_panel.php');
    exit();
}
$stmt = $conn->prepare("SELECT * FROM images WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$image = $result->fetch_assoc();
if (!$image) {
    header('Location: admin_panel.php?category=images');
    exit();
}
$stmt->close();
if (isset($_POST['submit'])) {
    $image_name = trim($_POST['Image_Name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $tags = trim($_POST['tags']);
    if (empty($image_name) || empty($category) || empty($description)) {
        $error = 'Все поля должны быть заполнены!';
    } else {
        $stmt = $conn->prepare("UPDATE images SET Image_Name = ?, Category = ?, Description = ?, Tags = ? WHERE ID = ?");
        $stmt->bind_param("ssssi", $image_name, $category, $description, $tags, $id);
        if ($stmt->execute()) {
            header('Location: admin_panel.php?category=images');
            exit();
        } else {
            $error = 'Ошибка при обновлении картинки!';
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="icons/icons.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <title>Pinspire | Redact</title>
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
                <h1>Редактирование</h1>
                <form action="" method="post" class="mt-3">
                <img src="../<?= $image['Path'] ?>" class="card-img-top mb-3 align-self-center" alt="..." style="width: 200px; height: 200px;">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">ID</label>
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="text" name="image_name" class="form-control" id="exampleFormControlInput1" value="<?= htmlspecialchars($image['ID'], ENT_QUOTES) ?>" placeholder="ID" required>
                        <label from="exampleFormControlInput1" class="form-label">Название</label>
                        <input type="text" name="Image_Name" class="form-control" id="exampleFormControlInput1" value="<?= htmlspecialchars($image['Image_Name'], ENT_QUOTES) ?>" placeholder="название" required>
                        <label for="exampleFormControlInput1" class="form-label">Описание</label>
                        <input type="text" name="description" class="form-control" id="exampleFormControlInput1" value="<?= htmlspecialchars($image['Description'], ENT_QUOTES) ?>" placeholder="описание" required>
                        <label for="exampleFormControlInput1" class="form-label">Теги</label>
                        <input type="text" name="tags" class="form-control" id="exampleFormControlInput1" value="<?= htmlspecialchars($image['Tags'], ENT_QUOTES) ?>" placeholder="Теги" required>
                        <label for="exampleFormControlInput1" class="form-label">Категория</label>
                        <select name="category" class="form-select" aria-label="Default select example" required>
                            <option selected><?= htmlspecialchars($image['Category'], ENT_QUOTES) ?></option>
                            <option value="Аниме">Аниме</option>
                            <option value="Игры">Игры</option>
                            <option value="Природа">Природа</option>
                            <option value="Музыка">Музыка</option>
                            <option value="Мемы">Мемы</option>
                            <option value="Машины">Машины</option>
                            <option value="Другое">Другое</option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Редактировать</button>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>