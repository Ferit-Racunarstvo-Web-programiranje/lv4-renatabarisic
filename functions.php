<?php

function db_connect() {
    $conn = mysqli_connect("localhost", "rbarisic", "rbwplv4password", "rbarisic");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

function getProductById($productId, $products) {
    foreach ($products as $product) {
        if ($product['code'] === $productId) {
            return $product;
        }
    }
    return null;
}

?>