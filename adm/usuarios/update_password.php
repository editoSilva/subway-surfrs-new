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

include './../../connection.php';
include './../../core/guards.php';

function get_form(): array
{
    return array(
        'id' => $_POST['id'],
        'password' => $_POST['password'],
        'password_confirmation' => $_POST['password_confirmation'],
    );
}

$form = get_form();
$errors = guard(
    $form,
    array(
        'id' => ['required', 'number'],
        'password' => ['required', 'lmax' => 256],
        'password_confirmation' => ['required', 'equals' => $form['password']],
    ),
    array(
        'id.required' => 'O id é obrigatório',
        'id.number' => 'O id deve ser um número',
        'password.required' => 'A senha é obrigatória',
        'password.lmax' => 'A senha deve ter no máximo 256 caracteres',
        'password_confirmation.required' => 'A confirmação de senha é obrigatória',
        'password_confirmation.equals' => 'A confirmação de senha deve ser igual a senha',
    )
);

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../usuarios");
    exit();
}

try {
    $form['password'] = password_hash($form['password'], PASSWORD_DEFAULT);

    update(
        'appconfig',
        array(
            'senha' => $form['password'],
        ),
        array(
            'id' => $form['id'],
        ),
    );

    $_SESSION['success'] = array('Senha atualizada com sucesso');
    header("Location: ../usuarios");
    exit();
} catch (Exception $e) {
    $_SESSION['errors'] = array('Ocorreu um erro ao atualizar a senha'. $e);
    header("Location: ../usuarios");
    exit();
}