<?php

session_start();

if (!isset($_SESSION['emailadm'])) {
    header("Location: ../login");
    exit();
}

# if is not a post request, exit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}


include '../../connection.php';
include '../../core/guards.php';

$conn = connect();

function validate($data)
{
    $errors = guard($data, [
        'deposito' => ['required', 'number', 'min' => 0, 'unique' => ['bonus', 'deposito']],
        'ganho' => ['required', 'number', 'min' => 0],
    ],['deposito.unique' => 'O valor de bônus de depósito já existe. Altere ele ao invés de criar um novo.'],);

    if (count($errors) > 0) {
        return $errors;
    }

    return [];
}

$errors = validate($_POST);

if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
} else {
    $_SESSION['success'][] = 'Bônus de depósito criado com sucesso';

    insert('bonus', [
        'deposito' => $_POST['deposito'],
        'ganho' => $_POST['ganho'],
    ]);
}

header('Location: ../bonus');
exit;