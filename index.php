<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korean Language Practice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        h2 {
            color: #007bff;
        }

        .btn {
            margin-right: 10px;
        }

        table {
            margin-top: 20px;
        }

        th, td {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Korean Practice</h2>
        <a class="btn btn-primary" href="/php/korean/input.php" role="button">Add Words</a>
        <a class="btn btn-primary" href="/php/korean/profile.php" role="button">View Profile</a>
        <a class="btn btn-primary" href="/php/korean/lesson.php" role="button">Generate Lesson</a>
        <a class="btn btn-primary" href="/php/korean/list.php" role="button">Wordlist</a>
        <br>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    
                    <th>Name</th>
                    <th>ELO</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "korean_practice";

                $connection = new mysqli($servername, $username, $password, $database);

                if ($connection->connect_error) {
                    die("Connection Failed:" . $connection->connect_error);
                }

                $sql = "SELECT * FROM users";
                $result = $connection->query($sql);

                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        
                        <td>$row[user_name]</td>
                        <td>$row[user_elo]</td>
                        
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>
