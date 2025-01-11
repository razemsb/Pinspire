<?php
$user = "root";
$password = "root";
$host = "localhost";
$database = "gallery";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}