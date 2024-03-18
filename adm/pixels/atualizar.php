<?php


require '../../connection.php';
require '../../core/guards.php';

$conn = connect();

function validate($data)
{
    $errors = guard($data, [
        'nome' => ['required', 'unique' => [
            'pixels', 'nome',
            ['id' => $data['id']]
        ]],
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
        'script.required' => 'O conteudo do pixel é obrigatório',
        'required' => 'O campo local é obrigatório',
        'in' => 'O campo local deve ser header ou footer',
    ]);

    if (count($errors) > 0) {
        return $errors;
    }

    return [];
}

function update_pixel_file($data)
{
    $path = get_by_id('pixels', $data['id'], ['script'])['script'];
    $content = $data['script'];

    // write the file to the uploads folder
    file_put_contents('../../uploads/pixels/' . $path, $content);
}

$errors = validate($_POST);


if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
} else {

    update_pixel_file($_POST);

    update('pixels', [
        'nome' => $_POST['nome'],
        'local' => $_POST['local'],
        'pagina' => $_POST['pagina']
    ], ['id' => $_POST['id']]);

    $_SESSION['success'] = 'Pixel atualizado com sucesso!';
}

echo json_encode(array('success' => true, 'message' => 'Bônus atualizado com sucesso'));
header('Location: ../pixels');
exit;