<?php

session_start();

if (!isset($_SESSION['emailadm'])) {
    header("Location: ../login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

include './../../connection.php';
include './../../core/guards.php';

function get_rules($name): array
{
    return match ($name) {
        'dificuldade_jogo' => [
            'required',
            'in' => ['facil', 'medio', 'dificil', 'impossivel'],
        ],
        'deposito_min',
        'saques_min',
        'saques_max',
        'aposta_max',
        'aposta_min' => [
            'required',
            'vmin' => 0,
             // 0.00 to 9999999999999999.99 || 0 to 9999999999999999
            'regex' => '/^(\d{1,16}(\.\d{1,2})?|\d{1,16})$/'
        ],
        'rollover_saque',
        'coin_value',
        'taxa_saque' => [
            'required',
            'vmin' => 0
        ],
        'xmeta' => [
            'required',
            'vmin' => 1
        ],
        'wppconnect-qrcode' => ['required'],
        '_' => [],
    };
}

function get_messages($name): array
{
    $key = $name;
    $name = match ($name) {
        'dificuldade_jogo' => 'dificuldade',
        'deposito_min' => 'depósito mínimo',
        'saques_min' => 'saques mínimo',
        'saques_max' => 'saques máximo',
        'aposta_max' => 'aposta máxima',
        'aposta_min' => 'aposta mínima',
        'rollover_saque' => 'rollover saque',
        'coin_value' => 'valor da moeda',
        'taxa_saque' => 'taxa saque',
        'xmeta' => 'meta de ganhos',
        'wppconnect-qrcode' => 'Conexão WhatsApp',
        '_' => '',
    };

    return match ($key) {
        'dificuldade_jogo' => [
            'dificuldade_jogo.required' => 'O campo dificuldade é obrigatório',
            'dificuldade_jogo.in' => 'O campo dificuldade deve ser facil, medio, dificil ou impossivel',
        ],
        'deposito_min',
        'saques_min',
        'saques_max',
        'aposta_max',
        'aposta_min' => [
            "$key.required" => "O campo $name é obrigatório",
            "$key.number" => "O campo $name deve ser numérico",
            "$key.vmin" => "O campo $name deve ser maior ou igual a 0",
            "$key.regex" => "O campo $name deve ser um número com até 2 casas decimais",
        ],
        'rollover_saque', 'taxa_saque', 'coin_value' => [
            'taxa_saque.required' => "O campo $name é obrigatório",
            'taxa_saque.number' => "O campo $name deve ser numérico",
            'taxa_saque.vmin' => "O campo $name deve ser maior ou igual a 0",
        ],
        'xmeta' => [
            'xmeta.required' => "O campo $name é obrigatório",
            'xmeta.number' => "O campo $name deve ser numérico",
            'xmeta.vmin' => "O campo $name deve ser maior ou igual a 1",
        ],
        'wppconnect-qrcode' => [
            'wppconnect-qrcode.required' => "O campo $name é obrigatório."
        ],
        '_' => [],
    };
}

function validate($name, $value): array
{
    $rules = get_rules($name);
    $messages = get_messages($name);

    return guard(
        [
            $name => $value,
        ],
        [
            $name => $rules
        ],
        $messages
    );
}

$form = [
    'option' => $_GET['opcao'],
    'value' => $_POST['valor'],
];

$errors = guard(
    $form,
    [
        'option' => [
            'required',
            'in' => [
                'dificuldade_jogo',
                'deposito_min',
                'saques_min',
                'saques_max',
                'aposta_max',
                'aposta_min',
                'rollover_saque',
                'taxa_saque',
                'coin_value',
                'xmeta',
                'wppconnect-qrcode'
            ]
        ],
        'value' => ['required'],
    ],
    [
        'option.required' => 'O campo opção é obrigatório',
        'option.in' => 'O campo opção é inválido',
        'value.required' => 'O campo valor é obrigatório',
    ]
);

if (!$errors) {
    $errors = validate($form['option'], $form['value']);
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    header("Location: ../configuracoes");
    exit();
}

try {
    if($form['option'] !== 'wppconnect-qrcode') {
        update('app', [
            $form['option'] => $form['value']
        ], []);
    } else {

    }

    $_SESSION['success'] = ['Variável atualizada com sucesso'];

    header("Location: ../configuracoes");
} catch (Exception $e) {
    $_SESSION['errors'] = ['Ocorreu um erro ao atualizar a a variável'];
    header("Location: ../configuracoes");
}