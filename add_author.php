<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$db = new mysqli('localhost', 'root', '', 'bnsh_biblioteka');

if ($db->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Błąd połączenia z bazą danych']);
    exit;
}

$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';

if (empty($first_name) || empty($last_name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Imię i nazwisko są wymagane']);
    exit;
}

$stmt = $db->prepare("INSERT INTO autorzy (first_name, last_name) VALUES (?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Błąd przygotowania zapytania']);
    exit;
}

$stmt->bind_param("ss", $first_name, $last_name);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Nie udało się dodać autora']);
}
?>
