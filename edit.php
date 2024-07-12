<?php

$servername="localhost";
$username="root";
$password="";
$database="korean_practice";

$connection = new mysqli($servername, $username, $password, $database);



$word_id = "";
$word_korean = "";
$word_trans = "";
$word_elo = "";


$errorMessage = "";
$successMessage = "";

if ( $_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"]) ) {
        header("location: /php/korean/list.php");
        exit;
    }

    $word_id = $_GET["id"];

    $sql = "SELECT * FROM words WHERE word_id=$word_id"; 
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: /php/korean/list.php");
        exit;
    }
    $word_korean = $row["word_korean"];
    $word_trans = $row["word_trans"];
    $word_elo = $row["word_elo"];
    

}
else {

    $word_id = $_POST["id"];
    $word_korean = $_POST["korean"];
    $word_trans = $_POST["translation"];
    $word_elo = $_POST["elo"];
    
    do {
        if ( empty($word_korean) || empty($word_trans) || empty($word_elo) ) {
            $errorMessage = "All the fields are required";
            break; 
        }
        
        $sql = "UPDATE words " . 
                "SET word_korean= '$word_korean', word_trans = '$word_trans', word_elo = '$word_elo' " .
                "WHERE word_id = $word_id";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $successMessage = "word updated correctly";
        
        header("location: /php/korean/list.php");
        exit;

    } while (true);



}

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korean Language Practice </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script scr="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container my-5">
        <h2>Update Words</h2>

        <?php 
        if ( !empty($errorMessage) ) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>    
            ";
        }
        ?>

        <form method="post">
            <input type="hidden" name="id" value="<?php echo $word_id; ?>">
            <div>
                <label class="col-sm-3 col-form-label">Korean</label>
                <div>
                    <input type="text" class="form-control" name="korean" value="<?php echo $word_korean; ?>">
                </div>
            </div>
            <div>
                <label class="col-sm-3 col-form-label">Translation</label>
                <div>
                    <input type="text" class="form-control" name="translation" value="<?php echo $word_trans; ?>">
                </div>
            </div>
            <div>
                <label class="col-sm-3 col-form-label">ELO</label>
                <div>
                    <input type="text" class="form-control" name="elo" value="<?php echo $word_elo; ?>">
                </div>
            </div>
            
            

            <?php 
            if (!empty($successMessage) ){
                echo "
                <div class='alert alert-succes alert-dismissible fade show' role='alert'>
                <strong>$successMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>   
                
                ";

            }
            ?>

            <div class="rowmb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/php/korean/index.php" role="button">Cancel</a>
                    
                </div>
            </div>
        </form>
    </div>
</body>
</html>