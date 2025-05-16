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
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center p-8">

    <h1 class="text-3xl font-bold mb-6">Edytuj książkę</h1>

    <form action="edit.php?id=<?php echo $id; ?>" method="post" class="bg-gray-800 p-6 rounded-lg shadow-md w-full max-w-lg">
        <div class="mb-4">
            <label for="title" class="block text-lg">Tytuł książki:</label>
            <input type="text" name="title" id="title" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-white" value="<?php echo $title; ?>" required>
        </div>
        <div class="mb-4">
            <label for="author_id" class="block text-lg">Autor:</label>
            <select name="author_id" id="author_id" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-white" required>
                <?php while ($author = $authors->fetch_assoc()) { ?>
                    <option value="<?php echo $author['ID']; ?>" <?php echo ($author['ID'] == $author_id) ? 'selected' : ''; ?>>
                        <?php echo $author['first_name'] . ' ' . $author['last_name']; ?>
                    </option>
                <?php } ?>
            </select>
            <button type="button" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 mt-3 rounded-lg text-white font-semibold" onclick="openModal()">Dodaj autora</button>
        </div>
        <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold">Zapisz zmiany</button>
    </form>

    <!-- Modal -->
    <div id="addAuthorModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Dodaj autora</h2>
            <form id="addAuthorForm">
                <div class="mb-3">
                    <label for="new_first_name" class="block">Imię:</label>
                    <input type="text" id="new_first_name" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-white" required>
                </div>
                <div class="mb-3">
                    <label for="new_last_name" class="block">Nazwisko:</label>
                    <input type="text" id="new_last_name" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-white" required>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white font-semibold" onclick="closeModal()">Anuluj</button>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white font-semibold" onclick="addAuthor()">Dodaj</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('addAuthorModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('addAuthorModal').classList.add('hidden');
        }

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
                        closeModal();
                    } else {
                        alert('Błąd podczas dodawania autora.');
                    }
                });
            }
        }
    </script>

</body>

</html>
