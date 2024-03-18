<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

require '../../connection.php';

$id = $_POST['id'];

delete('bonus', [
    'id' => $id
]);

$_SESSION['success'][] = 'Bônus deletado com sucesso';

header('Location: ../bonus');
exit;