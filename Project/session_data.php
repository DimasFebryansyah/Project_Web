<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["user"])) {
    echo json_encode(["error" => "User session not found"]);
    exit();
}

// Kirim data yang tersimpan di session
echo json_encode([
    "email" => $_SESSION["user"]["email"],
    "password" => $_SESSION["user"]["password"], // Hanya untuk modal
    "full_name" => $_SESSION["user"]["full_name"],
    "account_type" => $_SESSION["user"]["account_type"]
]);
?>