<?php
$conn = mysqli_connect("localhost", "root", "", "bnsh_biblioteka");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM ksiazki WHERE author LIKE 'a%'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }

            table, th, td {
                border: 1px solid black;
            }

            th, td {
                padding: 15px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <table>
            <tr>
                <th>ID</th>
                <th>Autor</th>
                <th>Tytuł</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['author'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </body>
</html>