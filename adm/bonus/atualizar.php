<?php

session_start();

require '../../connection.php';
require '../../core/guards.php';

$conn = connect();

function validate($data)
{
    $errors = guard($data, [
        'id' => ['required', 'number'],
        'deposito' => ['required', 'number', 'min' => 0],
        'ganho' => ['required', 'number', 'min' => 0],
    ]);

    if (count($errors) > 0) {
        return $errors;
    }

    return [];
}

$errors = validate($_POST);

if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
} else {

    update('bonus', [
        'deposito' => $_POST['deposito'],
        'ganho' => $_POST['ganho'],
    ], [
        'id' => $_POST['id']
    ]);

    $_SESSION['success'][] = 'Bônus atualizado com sucesso';
}

header('Location: ../bonus');
exit;