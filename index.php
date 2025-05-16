<?php
$db = new mysqli('localhost', 'root', '', 'bnsh_biblioteka');
$authors = $db->query("SELECT ID, first_name, last_name FROM autorzy");
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wyszukiwanie wg. autora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center p-8">

    <h1 class="text-4xl font-bold mb-6">Wyszukiwanie wg. autora</h1>

    <form action="index.php" method="post" class="w-full max-w-lg bg-gray-800 p-6 rounded-lg shadow-md">
        <div class="flex flex-col gap-4">
            <label for="name" class="text-lg">Nazwisko autora:</label>
            <input type="text" name="name" id="name" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-white">
            <div class="flex gap-4">
                <input type="submit" value="Szukaj" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white font-semibold cursor-pointer">
                <button type="button" id="addAuthor" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold">Dodaj autora</button>
                <button id="addBook" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white font-semibold">Dodaj książkę</button>
            </div>
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

    echo '<div class="w-full max-w-2xl mt-6">';
    echo '<table class="w-full border-collapse border border-gray-700">';
    echo "<tr class='bg-gray-800'>
            <th class='border border-gray-700 px-4 py-2'>ID</th>
            <th class='border border-gray-700 px-4 py-2'>Autor</th>
            <th class='border border-gray-700 px-4 py-2'>Tytuł</th>
            <th class='border border-gray-700 px-4 py-2'>Akcje</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $author = $row['author'];
        $title = $row['title'];
        echo "<tr class='bg-gray-700 border border-gray-600'>";
        echo "<td class='border border-gray-600 px-4 py-2'>$id</td>";
        echo "<td class='border border-gray-600 px-4 py-2'>$author</td>";
        echo "<td class='border border-gray-600 px-4 py-2'>$title</td>";
        echo "<td class='border border-gray-600 px-4 py-2 flex gap-2'>";
        echo "<a href='edit.php?id=$id' class='bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded-md text-black font-semibold'>✏️</a>";
        echo "<a href='delete.php?id=$id' class='bg-red-600 hover:bg-red-700 px-3 py-1 rounded-md text-white font-semibold'>🗑️</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo '</div>';
    ?>

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
                    <button type="button" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white font-semibold" onclick="closeAuthorModal()">Anuluj</button>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white font-semibold" onclick="addAuthor()">Dodaj</button>
                </div>
            </form>
        </div>
    </div>

    <div id="addBookModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Dodaj książkę</h2>
            <form id="addBookForm">
                <div class="mb-3">
                    <label for="new_book_title" class="block">Tytuł książki:</label>
                    <input type="text" id="new_book_title" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-white" required>
                </div>
                <div class="mb-3">
                    <label for="new_book_author" class="block">Autor:</label>
                    <select id="new_book_author" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md text-white" required>
                        <?php while ($author = $authors->fetch_assoc()) { ?>
                            <option value="<?= $author['ID'] ?>"><?= $author['first_name'] . ' ' . $author['last_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white font-semibold" onclick="closeBookModal()">Anuluj</button>
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white font-semibold" onclick="addBook()">Dodaj</button>
                </div>
            </form>
        </div>
    </div>

    

<script>
    const addAuthorBtn = document.getElementById('addAuthor');
    const addBookBtn = document.getElementById('addBook');
    const addAuthorModal = document.getElementById('addAuthorModal');
    const addBookModal = document.getElementById('addBookModal');

    addAuthorBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (addAuthorModal.classList.contains('hidden')) {
            addAuthorModal.classList.remove('hidden');
        } else {
            addAuthorModal.classList.add('hidden');
        }
    });

    addBookBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (addBookModal.classList.contains('hidden')) {
            addBookModal.classList.remove('hidden');
        } else {
            addBookModal.classList.add('hidden');
        }
    });

    function closeAuthorModal() {
        document.getElementById('addAuthorModal').classList.add('hidden');
    }

    function closeBookModal() {
        document.getElementById('addBookModal').classList.add('hidden');
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
                    let select = document.getElementById('new_book_author');
                    let option = document.createElement('option');
                    option.value = data.id;
                    option.textContent = firstName + ' ' + lastName;
                    select.appendChild(option);
                    select.value = data.id;
                    document.getElementById('new_first_name').value = '';
                    document.getElementById('new_last_name').value = '';
                    closeAuthorModal();
                } else {
                    alert('Błąd podczas dodawania autora.');
                }
            });
        }
    }

    function addBook() {
        let title = document.getElementById('new_book_title').value;
        let authorId = document.getElementById('new_book_author').value;

        if (title && authorId) {
            let formData = new FormData();
            formData.append('title', title);
            formData.append('author_id', authorId);

            fetch('add_book.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Książka została dodana!');
                    document.getElementById('new_book_title').value = '';
                    closeBookModal();
                    location.reload(); // lub odśwież wyniki
                } else {
                    alert('Błąd podczas dodawania książki.');
                }
            });
        }
    }
</script>

</body>

</html>
