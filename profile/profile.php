<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
function resizeImage($sourcePath, $destinationPath, $newWidth, $newHeight) {
    list($width, $height, $type) = getimagesize($sourcePath);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagecolortransparent($resizedImage, imagecolorallocate($resizedImage, 0, 0, 0));
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
    }

    imagecopyresampled(
        $resizedImage, 
        $sourceImage, 
        0, 0, 0, 0, 
        $newWidth, $newHeight, 
        $width, $height
    );

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($resizedImage, $destinationPath, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($resizedImage, $destinationPath);
            break;
        case IMAGETYPE_GIF:
            imagegif($resizedImage, $destinationPath);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($resizedImage);

    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['image_name'], $_POST['category'], $_POST['description'], $_FILES['image_file'], $_POST['tags'])) {
        $imageName = htmlspecialchars($_POST['image_name']);
        $category = $_POST['category'];
        $description = htmlspecialchars($_POST['description']);
        $tags = htmlspecialchars($_POST['tags']);
        $active = "active";
        $uploadUserId = $_SESSION['user']['ID'] ?? 0;

        $relativeDir = "uploads/";
        $previewDir = "preview/";
        $absoluteDir = __DIR__ . "/../uploads/";
        $absolutePreviewDir = __DIR__ . "/../preview/";

        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $originalFileName = $_FILES['image_file']['name'];
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;

        $absoluteFilePath = $absoluteDir . $newFileName;
        $relativeFilePath = $relativeDir . $newFileName;

        $absolutePreviewPath = $absolutePreviewDir . $newFileName;
        $relativePreviewPath = $previewDir . $newFileName;

        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0777, true);
        }
        if (!is_dir($absolutePreviewDir)) {
            mkdir($absolutePreviewDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $absoluteFilePath)) {

            if (!resizeImage($absoluteFilePath, $absolutePreviewPath, 300, 200)) {
                echo "<script>alert('Ошибка при создании превью!');</script>";
                exit();
            }

            $checkStmt = $conn->prepare("SELECT ID FROM images WHERE Image_Name = ?");
            $checkStmt->bind_param("s", $imageName);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $updateStmt = $conn->prepare("UPDATE images SET Path = ?, Preview_Path = ?, Category = ?, upload_user_id = ?, Description = ? WHERE Image_Name = ?");
                $updateStmt->bind_param("sssiss", $relativeFilePath, $relativePreviewPath, $category, $uploadUserId, $description, $imageName);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                $insertStmt = $conn->prepare("INSERT INTO images (Image_Name, Path, Preview_Path, Category, upload_user_id, Description, Tags, Active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insertStmt->bind_param("ssssssss", $imageName, $relativeFilePath, $relativePreviewPath, $category, $uploadUserId, $description, $tags, $active);
                $insertStmt->execute();
                $insertStmt->close();
            }

            $checkStmt->close();
            $conn->close();
            echo "<script>alert('Успешная загрузка!');</script>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "<script>alert('Ошибка при загрузке файла!');</script>";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        echo "<script>alert('Некорректные данные формы!');</script>";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileName = $_FILES['avatar']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = uniqid('avatar_', true) . '.' . $fileExtension;
        $uploadDir = dirname(__DIR__) . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destPath = $uploadDir . $newFileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $userId = $_SESSION['user']['ID'];
            $avatarUrl = 'uploads/' . $newFileName;
            $stmt = mysqli_prepare($conn, "UPDATE users SET Avatar = ? WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, 'si', $avatarUrl, $userId);
            mysqli_stmt_execute($stmt);
            $_SESSION['user']['Avatar'] = $avatarUrl;
            mysqli_stmt_close($stmt);
        }
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
        <a class="navbar-nav mt-1" style="font-weight: bold; font-size: 30px;" data-bs-toggle="modal" data-bs-target="#uploadModal"><img src="../icons/upload.svg" style="width: 30px; height: 30px; object-fit: cover;"></a>
        <div class="collapse navbar-collapse me-3" id="navbarcenter">
        <input type="text" name="search" id="search" class="form-control" placeholder="Поиск" style="width: 300px; margin-left: 20px;">
        </div>
        <div class="collapse navbar-collapse float-end" id="navbarNav">
            <ul class="navbar-nav me-5">
                <li class="nav-item ms-2 me-1">
                    <p class="mt-2 fw-bold" href="profile/profile.php"><?= htmlspecialchars($_SESSION['user']['Login'], ENT_QUOTES) ?></p>
                </li>
                <li class="nav-item mt-1 ms-3">
                    <a href="profile/profile.php"><img src="../<?= htmlspecialchars($_SESSION['user']['Avatar'], ENT_QUOTES) ?>" alt="" style="width: 50px; height: 50px; border-radius: 50%;"></a>
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
        <p class="mt-3 fw-bold ms-auto"><?= htmlspecialchars($_SESSION['user']['Login'], ENT_QUOTES) ?>
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
                <a class="nav-link" href="" data-bs-toggle="modal" data-bs-target="#uploadModal">Загрузить картинку</button>
            </li>
            <?php if($_SESSION['admin_auth'] === true): ?>
            <li class="list-group-item admin">
                <a class="nav-link mb-1" href="">Админ панель</a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <a class="danger btn btn-danger" href="../auth/logout.php" style="border-radius: 0px;">Выход</a>
</div>
<main>
<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-12 border shadow p-4">
            <h1>Профиль</h1>
            <div class="container mt-5 text-center">
                <div class="avatar-container mx-auto">
                    <img src="../<?= htmlspecialchars($_SESSION['user']['Avatar'], ENT_QUOTES) ?>" alt="Avatar" id="avatarImage">
                    <div class="avatar-menu">Поменять аватар</div>
                </div>
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
               <div class="col-md-3 col-sm-6 col-12">
                      <div class="card mt-3 mb-3 shadow-sm" style="display: none;">
                <img src="../<?= htmlspecialchars($image['Preview_Path'], ENT_QUOTES) ?>" class="card-img-top" alt="Изображение" style="width: 100%;">
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
                <?php
                 } 
            }
            ?>
            </div>
        </div>
    </div>
</div>
</main>
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Загрузка изображения...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data" method="POST" action="">
                        <div class="mb-3">
                            <label for="imageName" class="form-label">Название</label>
                            <input type="text" class="form-control" id="imageName" name="image_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="imageFile" class="form-label">Выбор изображения</label>
                            <input type="file" class="form-control" id="imageFile" name="image_file" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="tags" class="form-label">Теги</label>
                            <input type="text" class="form-control" id="tags" name="tags" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Категория</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="Аниме">Аниме</option>
                                <option value="Игры">Игры</option>
                                <option value="Природа">Природа</option>
                                <option value="Музыка">Музыка</option>
                                <option value="Мемы">Мемы</option>
                                <option value="Машины">Машины</option>
                                <option value="Другое">Другое</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание изображения</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <input type="hidden" name="upload_image" value="1">
                        <button type="submit" class="btn btn-primary">Загрузить</button>
                    </form>
                </div>
            </div>
        </div>
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
<div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-labelledby="changeAvatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changeAvatarModalLabel">Сменить аватар</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="avatarForm" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="avatarInput" class="form-label">Загрузите изображение</label>
              <input class="form-control" type="file" id="avatarInput" name="avatar" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-dark">Сохранить</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="../scripts/view.js"></script>
</body>
</html>