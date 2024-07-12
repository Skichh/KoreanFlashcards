<?php
if (isset($_GET["id"])) {
    $word_id = $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "korean_practice";

    $connection = new mysqli($servername, $username, $password, $database);

    // Check the connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "DELETE FROM words WHERE word_id=$word_id"; // Fix here
    $connection->query($sql);

    // Close the connection
    $connection->close();
}

header("location: /php/korean/list.php");
exit;
?>




