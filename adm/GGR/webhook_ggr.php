<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

# if is not a post request, exit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

function bad_request()
{
    http_response_code(400);
    exit;
}

# get the payload
$payload = file_get_contents('php://input');

# decode the payload
$payload = json_decode($payload, true);

# if the payload is not valid json, exit
if (is_null($payload)) {
    bad_request();
}

# if the payload is not a pix payment, exit
if ($payload['typeTransaction'] !== 'PIX') {
    bad_request();
}

function get_conn()
{
  include './../../connection.php';

  return new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

}

$externalReference = $payload['idTransaction'];
$status = $payload['statusTransaction'];

# if the payment is confirmed
if ($status === 'PAID_OUT') {
    $conn = get_conn();
    
    
    # get the payment from the database
    $sql = sprintf("SELECT * FROM ggr_deposito WHERE externalreference = '$externalReference'");
    $result = $conn->query($sql);
    $result = $result->fetch_assoc();
    
    #if the payment is not found, exit
    if (!$result) {
        bad_request();
    }

    #if the payment is already confirmed, exit
    if ($result['status'] === 'PAID_OUT') {
        bad_request();
    }	
    
    # update the payment status
    $sql = sprintf("UPDATE ggr_deposito SET status = 'PAID_OUT' WHERE externalreference = '%s'", $externalReference);
    $conn->query($sql);
    
	
	// GGR_`PAGO AUTOMATIZADO
	$valor_depositado = $result['valor'];
	$email = $result['email'];
	
	$sqlApp = sprintf("SELECT * FROM app limit 1");
    $resultApp = $conn->query($sqlApp);
    $resultApp = $resultApp->fetch_assoc();

    
	$sqlDeposito = sprintf("SELECT count(*) as total FROM ggr_deposito WHERE email = '{$email}'");
    $resultDeposito = $conn->query($sqlDeposito);
    
    $resultDeposito = $resultDeposito->fetch_assoc();
    
    $sqlGGR = sprintf("SELECT * FROM ggr limit 1");
    $resultGGR = $conn->query($sqlGGR);
    $GGR = $resultGGR->fetch_assoc();
    
    $valor_depositado = floatval($valor_depositado);
    $debito = floatval($GGR['debito_ggr']);
    $credito = floatval($GGR['credito_ggr']) + $valor_depositado;
    $pago = floatval($GGR['ggr_pago']);

    if($debito > 0) {
        if ($debito > $credito) {
            $pago = $pago + $credito;
            $debito = $debito - $credito;
            $credito = 0;
        } else {
            $credito = $credito - $debito;
            $pago = $pago + $debito;
            $debito = 0;
        }
    }
    
    $conn->query(sprintf("UPDATE ggr SET debito_ggr = '$debito'"));
    $conn->query(sprintf("UPDATE ggr SET credito_ggr = '$credito'"));
    $conn->query(sprintf("UPDATE ggr SET ggr_pago = '$pago'"));
    
    if($pago < $debito) {
        $conn->query(sprintf("UPDATE ggr SET status_ggr = 'IRREGULAR'"));
    } else {
        $conn->query(sprintf("UPDATE ggr SET status_ggr = 'REGULAR'"));
    }
    
    # return a success response
    var_dump(json_encode(array('success' => true, 'message' => 'Pagamento do PIX confirmado.')));
    http_response_code(200);
    exit;
}

