<?php


include_once '../connection.php';

# if is not a post request, exit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}


try {
    $email = $_POST['email'];

    if (!isset($email)) {
        echo json_encode([]);
        exit;
    }

    $all = get_all('saques', ['email' => $email]);
    echo json_encode($all);
} catch (Exception $e) {
     echo json_encode([]);
    http_response_code(200);
}