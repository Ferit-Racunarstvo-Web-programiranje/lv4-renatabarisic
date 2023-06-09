<?php

function db_connect() {
    $conn = mysqli_connect("localhost", "rbarisic", "rbwplv4password", "rbarisic");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_close($conn);
}

?>