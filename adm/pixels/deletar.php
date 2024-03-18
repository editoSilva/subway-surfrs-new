<?php

require '../../connection.php';

$conn = connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$id = $_POST['id'];

function delete_pixels_file($id)
{
    $file = get_by_id('pixels', $id, ['script'])['script'];
    $path = '../../uploads/pixels/' . $file;

    // remove file
    unlink($path);
}

delete_pixels_file($_POST['id']);

delete('pixels', [
    'id' => $id
]);

$_SESSION['success'] = 'Pixel deletado com successo!';

header('Location: ../pixels');
exit;