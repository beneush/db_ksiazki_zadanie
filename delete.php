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
            header("Location: index.php?action=deleted");
            exit;
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
    <title>Potwierdzenie usunięcia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white flex flex-col items-center justify-center min-h-screen p-8">

    <div class="bg-gray-800 p-6 rounded-lg shadow-md max-w-lg text-center">
        <h1 class="text-2xl font-bold mb-4">Potwierdzenie usunięcia</h1>
        <p class="mb-4">Czy na pewno chcesz usunąć książkę <strong class="text-red-400"><?php echo $title; ?></strong> autorstwa <strong class="text-blue-400"><?php echo $author; ?></strong>?</p>
        
        <form action="delete.php?id=<?php echo $id; ?>" method="post" class="flex justify-center space-x-4">
            <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white font-semibold">Tak, usuń</button>
            <a href="index.php" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-white font-semibold">Anuluj</a>
        </form>
    </div>

</body>

</html>
