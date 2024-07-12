<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Access the user_id from the session
$user_id = $_SESSION["user_id"];

// Access additional user information, if available
$additional_info = isset($_SESSION["additional_user_info"]) ? $_SESSION["additional_user_info"] : [];

// Include your existing code here
// ...

// Check if there is no POST request and session cards exist, then clear it
if (empty($_POST) && isset($_SESSION["cards"])) {
    unset($_SESSION["cards"]);
    unset($_SESSION["count"]);
    unset($_SESSION["current_question"]);
    unset($_SESSION["current_translation"]);
    unset($_SESSION["current_elo"]);
}

function Question($count, $cards)
{
    $currentCard = $cards[$count];
    $question = $currentCard['word_korean'];
    $answer = $currentCard['word_trans'];
    $elo = $currentCard['word_elo']; // Assuming 'word_elo' is the column name for word elo

    echo "<div class='question-container'>";
    echo "<h2>Question " . ($count + 1) . ":</h2>";
    echo "<p class='question'>$question</p>";
    echo "<p class='difficulty'>Difficulty: $elo</p>";
    echo '<form method="post">';
    echo '<input type="text" name="user_answer" required>';
    echo '<input type="submit" value="Submit">';
    echo '</form>';
    echo "</div>";

    $_SESSION["current_question"] = $question;
    $_SESSION["current_translation"] = $answer;
    $_SESSION["current_elo"] = $elo;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "korean_practice";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming user_elo is the user's current elo fetched from the database
$user_id = $_SESSION["user_id"];
$query = "SELECT user_elo FROM users WHERE user_id = $user_id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_elo = $row['user_elo'];
} else {
    // Handle the case where the user's elo is not found
    $user_elo = 0; // Default elo or any other appropriate value
}

// Calculate the elo range
$elo_lower_limit = $user_elo - 100;
$elo_upper_limit = $user_elo + 100;

// Check if cards are not set or if it's an empty array
if (empty($_SESSION["cards"])) {
    $query = "SELECT * FROM words WHERE word_elo BETWEEN $elo_lower_limit AND $elo_upper_limit ORDER BY RAND() LIMIT 10";
    $result = $conn->query($query);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    $cards = $result->fetch_all(MYSQLI_ASSOC);
    $_SESSION["cards"] = $cards;
    $_SESSION["count"] = 0; // Reset the counter when starting a new quiz session
    Question($_SESSION["count"], $_SESSION["cards"]);
}

// Display the current question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_answer = isset($_POST['user_answer']) ? $_POST['user_answer'] : '';

    // Check user's answer
    $current_question = $_SESSION["current_question"];
    $current_translation = $_SESSION["current_translation"];
    $current_elo = $_SESSION["current_elo"];

    echo "<div class='result-container'>";
    echo " <p>Question: $current_question</p>";
    echo " <p>Translation: $current_translation</p>";
    echo " <p>User Answer: $user_answer</p>";
    echo " <p>Difficulty: $current_elo</p>";

    if ($user_answer === $current_translation) {
        echo " <p class='correct'>Correct!</p>";
    } else {
        echo " <p class='incorrect'>Incorrect. The correct answer is: $current_translation</p>";
    }

    echo "</div>";

    $_SESSION["user_responses"][$_SESSION["count"]] = [
        'question' => $current_question,
        'translation' => $current_translation,
        'user_answer' => $user_answer,
        'elo' => $current_elo
    ];

    $_SESSION["count"]++;

    if ($_SESSION["count"] < 10) {
        // Display the next question
        Question($_SESSION["count"], $_SESSION["cards"]);
    } else {
        // Redirect to results.php with an array of all questions, translations, and user answers
        header("Location: results.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korean Practice Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .question-container {
            max-width: 600px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            text-align: center;
        }

        h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .question {
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .difficulty {
            color: #888;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 200px;
        }

        input[type="submit"] {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .result-container {
            max-width: 600px;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            text-align: center;
        }

        .correct {
            color: #4CAF50;
        }

        .incorrect {
            color: #ff0000;
        }
    </style>
</head>
<body>

</body>
</html>
