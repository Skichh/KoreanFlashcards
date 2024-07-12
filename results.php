<?php

session_start();

if (!isset($_SESSION["user_responses"])) {
    header("Location: index.php"); // Redirect to the main page if there are no user responses
    exit();
}

$user_responses = $_SESSION["user_responses"];

// Display user responses
echo "<h2>User Responses</h2>";

foreach ($user_responses as $index => $response) {
    $question = $response['question'];
    $translation = $response['translation'];
    $user_answer = $response['user_answer'];
    $elo = $response['elo']; // Added word_elo

    echo "<p><strong>Question " . ($index + 1) . ":</strong> $question</p>";
    echo "<p>Translation: $translation</p>";
    echo "<p>User Answer: $user_answer</p>";
    echo "<p>Difficulty : $elo</p>"; // Display word_elo
    echo "<hr>";
}

// Calculate and display overall score
$correct_answers = 0;

foreach ($user_responses as $response) {
    if ($response['user_answer'] === $response['translation']) {
        $correct_answers++;
    }
}

$total_questions = count($user_responses);
$score = ($correct_answers / $total_questions) * 100;


$servername = "localhost";
$username = "root";
$password = "";
$database = "korean_practice";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$user_id = $_SESSION["user_id"];
$query = "SELECT user_elo FROM users WHERE user_id = $user_id";
$result = $conn->query($query);


$row = $result->fetch_assoc();



if ($score >= 60) {
    $row['user_elo'] += 10;
} elseif ($score <= 40) {
    $row['user_elo'] -= 10;
}


$new_elo = $row['user_elo'];

$sql = "UPDATE users SET user_elo= $new_elo WHERE user_id=1";
$result = $conn->query($sql);




echo "<h3>Your Score:</h3>";
echo "<p>Correct Answers: $correct_answers / $total_questions</p>";
echo "<p>Score: $score%</p>";
echo '<form action="index.php" method="get">';
echo '<input type="submit" value="Go to Profile">';
echo '</form>';

// Clear the session to reset the quiz
unset($_SESSION["cards"]);
unset($_SESSION["count"]);
unset($_SESSION["current_question"]);
unset($_SESSION["current_translation"]);
unset($_SESSION["user_responses"]);
?>

