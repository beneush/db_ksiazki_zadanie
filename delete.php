<?php
$db = new mysqli('localhost', 'root', '', 'bnsh_biblioteka');

// Check if the ID is passed
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Handle deletion
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $sql = "DELETE FROM ksiazki WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Książka została usunięta.";
        } else {
            echo "Błąd podczas usuwania książki.";
        }
    } else {
        $sql = "SELECT ksiazki.title, CONCAT(autorzy.first_name, ' ', autorzy.last_name) AS author
                FROM ksiazki 
                LEFT JOIN autorzy ON autorzy.ID = ksiazki.author_id 
                WHERE ksiazki.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $title = $row['title'];
            $author = $row['author'];
        } else {
            echo "Nie znaleziono książki.";
            exit;
        }
    }
} else {
    echo "Brak ID książki.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie usunięcia książki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Potwierdzenie usunięcia książki</h1>
        <p>Czy na pewno chcesz usunąć książkę <strong><?php echo $title; ?></strong> autorstwa <strong><?php echo $author; ?></strong>?</p>
        <form action="delete.php?id=<?php echo $id; ?>" method="post">
            <button type="submit" class="btn btn-danger">Tak, usuń książkę</button>
            <a href="index.php" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</body>

</html>
