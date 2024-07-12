<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "korean_practice";

$connection = new mysqli($servername, $username, $password, $database);

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $korean_words_input = $_POST["korean_words"];

    // Split the input into an array of Korean words based on commas
    $korean_words = explode(",", $korean_words_input);

    foreach ($korean_words as $word) {
        // Remove leading and trailing spaces
        $word = trim($word);

        // Skip empty words
        if (empty($word)) {
            continue;
        }

        $word_elo = calculateELO(mb_strlen($word, 'UTF-8'));

        do {
            // Translation logic using Google Translate API
            $translation = translateKoreanToEnglish($word);

            if (!$translation) {
                $errorMessage = "Failed to translate the Korean word";
                break;
            }

            $word_trans = $translation;

            // Insert into the database
            $sql = "INSERT INTO words (word_korean, word_trans, word_elo)" .
                "VALUES ('$word', '$word_trans', '$word_elo')";
            $result = $connection->query($sql);

            if (!$result) {
                $errorMessage = "Invalid query: " . $connection->error;
                break;
            }

            $successMessage = "Words added correctly";
        } while (false);
    }

    // Redirect to the index page after processing all submitted words
    header("location: /php/korean/input.php");
    exit;
}

function calculateELO($hangul_character_count) {
    // Adjust the ELO calculation based on your requirements
    // For simplicity, use a linear mapping from Hangul character count to ELO in the range [900, 1500]
    return max(900, min(1500, 900 + $hangul_character_count * 100));
}

function translateKoreanToEnglish($korean)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://google-translate1.p.rapidapi.com/language/translate/v2",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => 'q=' . $korean . '&target=en&source=ko',
        CURLOPT_HTTPHEADER => [
            "Accept-Encoding: application/gzip",
            "X-RapidAPI-Host: google-translate1.p.rapidapi.com",
            "X-RapidAPI-Key: b10376afacmshf62311f19103c7dp142115jsn51a9da2d453b  ",
            "content-type: application/x-www-form-urlencoded"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return false;
    } else {
        $data = json_decode($response, true);
        return $data['data']['translations'][0]['translatedText'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korean Practice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container my-5">
        <h2>Add Words</h2>

        <?php 
        if (!empty($errorMessage)) {
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>    
            ";
        }
        ?>

        <form action="/php/korean/input.php" method="post" enctype="multipart/form-data">
            <div>
                <label class="col-sm-3 col-form-label">Korean Words</label>
                <div>
                    <input type="text" class="form-control" name="korean_words">
                </div>
            </div>

            <?php 
            if (!empty($successMessage)) {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <strong>$successMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>   
                ";
            }
            ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3">
                    <a class="btn btn-outline-primary" href="/php/korean/index.php" role="button">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
