<?php
$db = new mysqli('localhost', 'root', '', 'bnsh_biblioteka');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT ksiazki.id, ksiazki.title, ksiazki.author_id, autorzy.first_name, autorzy.last_name 
            FROM ksiazki 
            LEFT JOIN autorzy ON autorzy.ID = ksiazki.author_id 
            WHERE ksiazki.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $title = $row['title'];
        $author_id = $row['author_id'];
    } else {
        echo "Nie znaleziono książki.";
        exit;
    }
} else {
    echo "Brak ID książki.";
    exit;
}

$authors = $db->query("SELECT ID, first_name, last_name FROM autorzy ORDER BY last_name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_title = $_POST['title'];
    $new_author_id = $_POST['author_id'];

    $update_sql = "UPDATE ksiazki SET title = ?, author_id = ? WHERE id = ?";
    $stmt = $db->prepare($update_sql);
    $stmt->bind_param("sii", $new_title, $new_author_id, $id);

    if ($stmt->execute()) {
        header("Location: index.php?action=edited");
    } else {
        echo "Błąd podczas aktualizacji.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj książkę</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Edytuj książkę</h1>
        <form action="edit.php?id=<?php echo $id; ?>" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Tytuł książki:</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo $title; ?>" required>
            </div>
            <div class="mb-3">
                <label for="author_id" class="form-label">Autor:</label>
                <select name="author_id" id="author_id" class="form-control" required>
                    <?php while ($author = $authors->fetch_assoc()) { ?>
                        <option value="<?php echo $author['ID']; ?>" <?php echo ($author['ID'] == $author_id) ? 'selected' : ''; ?>>
                            <?php echo $author['first_name'] . ' ' . $author['last_name']; ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="button" class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#addAuthorModal">Dodaj autora</button>
            </div>
            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
        </form>
    </div>

    <div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAuthorModalLabel">Dodaj autora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAuthorForm">
                        <div class="mb-3">
                            <label for="new_first_name" class="form-label">Imię:</label>
                            <input type="text" id="new_first_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_last_name" class="form-label">Nazwisko:</label>
                            <input type="text" id="new_last_name" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addAuthor()">Dodaj</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addAuthor() {
            let firstName = document.getElementById('new_first_name').value;
            let lastName = document.getElementById('new_last_name').value;
            
            if (firstName && lastName) {
                let formData = new FormData();
                formData.append('first_name', firstName);
                formData.append('last_name', lastName);
                
                fetch('add_author.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let select = document.getElementById('author_id');
                        let option = document.createElement('option');
                        option.value = data.id;
                        option.textContent = firstName + ' ' + lastName;
                        select.appendChild(option);
                        select.value = data.id;
                        document.getElementById('new_first_name').value = '';
                        document.getElementById('new_last_name').value = '';
                        let modal = bootstrap.Modal.getInstance(document.getElementById('addAuthorModal'));
                        modal.hide();
                    } else {
                        alert('Błąd podczas dodawania autora.');
                    }
                });
            }
        }
    </script>
</body>

</html>