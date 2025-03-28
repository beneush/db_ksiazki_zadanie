<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

    <h1>wyszukiwanie wg. autora</h1>
    <form action="zadanie.php" method="post">
        <div class="row">
        <label for="name" class="col form-label">Nazwisko autora:</label>
        <input type="text" name="name" id="name" class="col form-control">
        <input type="submit" value="Szukaj" class="col btn btn-primary">
        </div>
    </form>
    
    <?php
    $name = "%";
    if (isset($_POST['name'])) {
        $name = "%" . $_POST['name'] . "%";
    }
    $sql = "SELECT 
        ksiazki.id,
        CONCAT(autorzy.first_name, ' ', autorzy.last_name) AS author, 
        ksiazki.title AS title FROM ksiazki 
        LEFT JOIN autorzy ON autorzy.ID = ksiazki.author_id 
        WHERE autorzy.last_name LIKE '" . $name . "' 
        OR autorzy.first_name LIKE '" . $name . "'";

    $db = new mysqli('localhost', 'root', '', 'bnsh_biblioteka');
    $result = $db->query($sql);
    echo '<table class="table">';
    echo "<tr><th>ID</th><th>Autor</th><th>Tytuł</th><th>Akcje</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $author = $row['author'];
        $title = $row['title'];
        $edit = "<button class=\"btn btn-warning\" onclick=\"location.href='edit.php?id=$id'\" type=\"button\"><i class=\"bi bi-pencil\"></i></button>";
        $delete = "<button class=\"btn btn-danger\" onclick=\"location.href='delete.php?id=$id'\" type=\"button\"><i class=\"bi bi-trash3\"></i></button>";
        echo "<tr>";
        echo "<td>$id</td><td>$author</td><td>$title</td>";
        echo "<td>$edit $delete</td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>