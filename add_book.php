<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$db = new mysqli('localhost', 'root', '', 'bnsh_biblioteka');

if ($db->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych']);
    exit;
}

$title = $_POST['title'] ?? '';
$author_id = $_POST['author_id'] ?? '';

if (!$title || !$author_id) {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych']);
    exit;
}

$stmt = $db->prepare("INSERT INTO ksiazki (title, author_id) VALUES (?, ?)");
$stmt->bind_param("si", $title, $author_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Nie udało się dodać książki']);
}
?>
