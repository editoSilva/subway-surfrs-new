<?php

session_start();

if (!isset($_SESSION['emailadm'])) {
    header("Location: ../login");
    exit();
}

require './../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable('./../../');
$dotenv->load();

require './../../connection.php';
require './../../core/guards.php';

$errors = guard(
    $_GET,
    ['id' => ['required', 'exists' => ['saques', 'id']]],
    [
        'id.required' => 'ID do saque é obrigatório',
        'id.exists' => 'Saque não encontrado'
    ]
);

if ($errors) {
    $_SESSION['errors'] = $errors;
    header('Location: ../saques-afiliados');
    exit;
}

try {
    $withdraw = get_by('saque_afiliado', ['id' => $_GET['id']]);

    update('saque_afiliado', [
        'status' => 'MANUALLY_PAID_OUT',
        'approved_at' => date('Y-m-d H:i:s')
    ], ['id' => $_GET['id']]);

    stmt("UPDATE appconfig SET sacou_saldo = sacou_saldo + ?, pix_gerado = 0 WHERE email = ?",
        'is',
        [$withdraw['valor'], $withdraw['email']]);

    $url = $_ENV['BASE_URL'];
    $msg = "Status do saque alterado com sucesso";
    header("Location: $url/adm/saques-afiliados");
    $_SESSION['success'] = [$msg];
} catch (Exception  $ex) {

}