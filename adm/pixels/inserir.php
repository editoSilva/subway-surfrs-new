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

var_dump($_POST);

function validate($data)
{
    $errors = guard($data, [
        'nome' => ['required', 'unique' => ['pixels', 'nome']],
        'script' => [
            'required',
        ],
        'local' => [
            'required',
            'in' => ['header', 'body'],
        ]
    ], [
        'nome.required' => 'O campo nome é obrigatório',
        'nome.unique' => 'Já existe um pixel com esse nome',
        'arquivo.required' => 'O conteudo do pixel é obrigatório',
        'arquivo.regex' => 'O conteudo do pixel não pode conter tags script. Se você está tentando importar mais de um pixel, por favor, importe um por vez.',
        'required' => 'O campo local é obrigatório',
        'in' => 'O campo local deve ser header ou footer',
    ]);

    if (count($errors) > 0) {
        return $errors;
    }

    return [];
}


function store_pixel_file($data)
{
    $path = '../../uploads/pixels/';
    $name = $data['nome'];
    $content = $data['script'];

    // make a file name based on unique id
    $filename = $path . $name . '.js';
    // hash the name
    $hashed_filename = md5($filename . microtime()) . '.js';

    // write the file to the uploads folder
    $upload = file_put_contents($path . $hashed_filename, $content);

    if ($upload) {
        return $hashed_filename;
    }

    return null;
}

$errors = validate($_POST);

if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
} else {
    $file = store_pixel_file($_POST);

    if ($file) {
        insert('pixels', [
            'nome' => $_POST['nome'],
            'script' => $file,
            'local' => $_POST['local'],
            'pagina' => $_POST['pagina']
        ]);

        $_SESSION['success'] = 'Pixel atualizado com sucesso!';

    } else {
        $_SESSION['errors'] = ['Ocorreu um erro ao salvar o arquivo'];
    }
}

header('Location: ../pixels');
exit;