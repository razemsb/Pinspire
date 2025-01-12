<?php
function banUser(int $user_id): bool
{
    $conn = connectToDatabase();
    $stmt = $conn->prepare("UPDATE users SET is_active = 'not_active' WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    return true;
}

function unbanUser(int $user_id): bool
{
    $conn = connectToDatabase();
    $stmt = $conn->prepare("UPDATE users SET is_active = 'active' WHERE ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    return true;
}

function banImage(int $image_id): bool
{
    $conn = connectToDatabase();
    $stmt = $conn->prepare("UPDATE images SET is_active = 'not_active' WHERE ID = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->close();
    return true;
}

function unbanImage(int $image_id): bool
{
    $conn = connectToDatabase();
    $stmt = $conn->prepare("UPDATE images SET is_active = 'active' WHERE ID = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->close();
    return true;
}
?>