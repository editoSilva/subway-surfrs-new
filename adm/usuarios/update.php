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

$conn = connect();

function get_form(): array
{

    return array(
        'id' => $_POST['id'],
        'saldo' => floatval($_POST['saldo']),
        'revenue_share' => floatval(str_replace(',', '.', $_POST['revenue_share'])),
        'cpa' => floatval($_POST['cpa']),
        'cpafake' => floatval(str_replace(',', '.', $_POST['cpafake'])),
        'comissaofake' => floatval(str_replace(',', '.', $_POST['comissaofake'])),
        'bloc' => isset($_POST['bloqueado']) && $_POST['bloqueado'] === 'on' ? 1 : 0,
        'dificuldade' => $_POST['dificuldade'],
        'coin_ind' => floatval($_POST['coinInd']),
        'xmeta_ind' => $_POST['metaInd'],
        'saldo_fake' => floatval($_POST['saldo_fake']),
    );
}

$form = get_form();

$errors = guard(
    $form,
    array(
        'id' => ['required', 'number'],
        'saldo' => ['required', 'number', 'vmin' => 0],
        'revenue_share' => ['required', 'vmin' => 0, 'vmax' => [100]],
        'cpa' => ['required', 'number', 'vmin' => 0],
        'cpafake' => ['required', 'number', 'vmin' => 0, 'vmax' => 100],
        'comissaofake' => ['required', 'number', 'vmin' => 0, 'vmax' => 100],
        'bloc' => ['required', 'number', 'in' => [0, 1]],
        'dificuldade' => ['required', 'in' => ['facil', 'medio', 'dificil', 'impossivel', 'nenhuma']],
        'xmeta_ind' => ['required', 'number' , 'vmin' => 0],
        'coin_ind' => ['required', 'number' , 'vmin' => 0],
        'saldo_fake' => ['required', 'number', 'vmin' => 0],
    ),
    array(
        'id.required' => 'O id é obrigatório',
        'id.number' => 'O id deve ser um número',
        'saldo.required' => 'O saldo é obrigatório',
        'saldo.number' => 'O saldo deve ser um número',
        'saldo.vmin' => 'O saldo deve ser maior ou igual a 0',
        'revenue_share.required' => 'O revenue share é obrigatório',
        'revenue_share.number' => 'O revenue share deve ser um número',
        'revenue_share.vmin' => 'O revenue share deve ser maior ou igual a 0',
        'revenue_share.vmax' => 'O revenue share deve ser menor ou igual a 100',
        'cpa.required' => 'O CPA é obrigatório',
        'cpa.number' => 'O CPA deve ser um número',
        'cpa.vmin' => 'O CPA deve ser maior ou igual a 0',
        'cpafake.required' => 'O CPA fake é obrigatório',
        'cpafake.number' => 'O CPA fake deve ser um número',
        'cpafake.vmin' => 'O CPA fake deve ser maior ou igual a 0',
        'cpafake.vmax' => 'O CPA fake deve ser menor ou igual a 100',
        'comissaofake.required' => 'A comissão fake é obrigatória',
        'comissaofake.number' => 'A comissão fake deve ser um número',
        'comissaofake.vmin' => 'A comissão fake deve ser maior ou igual a 0',
        'comissaofake.vmax' => 'A comissão fake deve ser menor ou igual a 100',
        'bloc.required' => 'O bloqueado é obrigatório',
        'bloc.number' => 'O bloqueado deve ser um número',
        'bloc.in' => 'O bloqueado deve ser 0 ou 1',
        'dificuldade.required' => 'A dificuldade é obrigatória',
        'dificuldade.in' => 'A dificuldade deve ser facil, medio, dificil, impossivel ou nenhuma',
        'saldo_fake.required' => 'O saldo fake é obrigatório',
        'saldo_fake.number' => 'O saldo fake deve ser um número',
        'saldo_fake.vmin' => 'O saldo fake deve ser maior ou igual a 0',
    )
);

if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
    header("Location: ../usuarios");
    exit;
}

$_SESSION['success'] = [];

try {
    # when the user balance is updated, the rollover is reseted
    $user = get_by_id('appconfig', $form['id']);

    $balance = $form['saldo'];
    $fake_balance = $form['saldo_fake'];
    $total_balance = $balance + $fake_balance;

    if ($user['saldo'] != $form['saldo'] || $user['saldo_fake'] != $form['saldo_fake']) {
        $app = get_all('app')[0];
        $rol_total = (floatval($app['rollover_saque']) / 100) * $total_balance;

        $form['rollover_total'] = $rol_total;
        $form['rollover'] = $rol_total;

        $_SESSION['success'] = array_merge(
            $_SESSION['success'],
            ["O rollover do usuário foi atualizado para R$" . number_format($rol_total, 2, ',')]
        );
    }


    update('appconfig', $form, ['id' => $form['id']]);
} catch (Exception $ex) {
    $_SESSION['errors'] = ["Erro na atualização dos dados."];
    var_dump($ex);
    exit;
}

$_SESSION['success'] = array_merge(
    $_SESSION['success'],
    ["Usuário atualizado com sucesso"]
);

if ($form['revenue_share'] > 0 && $form['revenue_share'] < 1) {
    $_SESSION['warning'] = array_merge(
        $_SESSION['warning'],
        ["Usuário atualizado com sucesso"]["O valor a porcentagem do revenue share é inferior a 1. Certifique-se que o valor está correto. o valor atual é: " . $form["revenue_share"] / 100 . "%"]
    );
}

if ($form['cpafake'] > 0 && $form['cpafake'] < 1) {
    $_SESSION['warning'] = array_merge(
        $_SESSION['warning'],
        ["O valor a porcentagem do CPA fake é inferior a 1. Certifique-se que o valor está correto. o valor atual é: " . $form["cpafake"] / 100 . "%"]
    );
}

if ($form['comissaofake'] > 0 && $form['comissaofake'] < 1) {
    $_SESSION['warning'] = array_merge(
        $_SESSION['warning'],
        ["O valor a porcentagem da comissão fake é inferior a 1. Certifique-se que o valor está correto. o valor atual é: " . $form["comissaofake"] / 100 . "%"]
    );
}

header("Location: ../usuarios");


