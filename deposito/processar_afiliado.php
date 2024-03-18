<?php



session_start();

include './../conectarbanco.php';

$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
$token = trim($_GET['token']);

$sql = sprintf("SELECT * FROM confirmar_deposito WHERE externalreference = '%s'", $token);
$result3 = $conn->query($sql)->fetch_assoc();

$sql = "SELECT * FROM app;";
$result5 = $conn->query($sql);
$result6 = $result5->fetch_assoc();

$sql = sprintf("SELECT * FROM appconfig WHERE email = '%s';", $result3['email']);
$result2 = $conn->query($sql);
$result = $result2->fetch_assoc();

if($result['status_primeiro_deposito'] !== "1" ){
    if(intval($result3['valor']) >= intval($result6['deposito_min_cpa'])){
        $randomNumber = rand(0, 100);
        if($result['cpafake'] > 0 ? ($randomNumber <= intval($result['cpafake'])) : ($randomNumber <= intval($result6['chance_afiliado']))) {
            $sql_deposito_cpa = sprintf("UPDATE appconfig SET status_primeiro_deposito = '1' WHERE email = '%s'", $result3['email']); 
            $conn->query($sql_deposito_cpa);
            if(intval($result['cpa']) > 0){
                $sql_soma_cpa = sprintf("UPDATE appconfig SET saldo_cpa = saldo_cpa + '%d' WHERE linkafiliado = 'https://subwaydin.app/cadastrar/?aff=%s'", intval($result['cpa']), $result['lead_aff']);
                $conn->query($sql_soma_cpa);
            }else{
                $sql_soma_cpa = sprintf("UPDATE appconfig SET saldo_cpa = saldo_cpa + '%d' WHERE linkafiliado = 'https://subwaydin.app/cadastrar/?aff=%s'", intval($result6['cpa']), $result['lead_aff']);
                $conn->query($sql_soma_cpa);
            }
        }
    }
    header('Location: ../painel');
    exit;
}

header('Location: ../painel');
exit;

