<?php


session_start();

global $split;
include './../../split_ggr.php';
require './../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./../../');
$dotenv->safeLoad();

if (!isset($_SESSION['emailadm'])) {
    $url = $_ENV['BASE_URL'];
    header("Location: $url/adm/login");
    exit();
}


$email = $_SESSION['emailadm'];

include './../../connection.php';

$conn = connect();

function get_form()
{
    return array(
        'name' => $_POST['name'],
        'cpf' => $_POST['document'],
        'value' => $_POST['valor_transacao'],
    );
}

function validate_form($form)
{
    global $depositoMinimo;

    $errors = array();

    if (empty($form['name'])) {
        $errors['name'] = 'O nome é obrigatório';
    }

    if (empty($form['cpf'])) {
        $errors['cpf'] = 'O CPF é obrigatório';
    }

    if (empty($form['value'])) {
        $errors['value'] = 'O valor é obrigatório';
    } else if ($form['value'] < $depositoMinimo) {
        $errors['value'] = 'O valor mínimo é de R$ ' . $depositoMinimo;
    }

    return $errors;
}

$pixGerado = false;
$pix_key = false;
$token = false;

function make_request($url, $payload, $method = 'POST')
{
    $headers = array(
        "Content-Type: application/json",
        "ci: " . $_ENV['CLIENT_ID_GGR'],
        "cs: " . $_ENV['CLIENT_SECRET_GGR']
    );

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    curl_close($ch);
    return $result;
}

function make_pix($name, $cpf, $value)
{
    global $split;
    $dueDate = date('Y-m-d', strtotime('+1 day'));
    $email = 'cliente@email.com';

    $payload = array(
        'requestNumber' => '12356',
        'dueDate' => $dueDate,
        'amount' => floatval($value),
        'client' => array(
            'name' => $name,
            'email' => $email,
            'document' => $cpf,
        ),
        'callbackUrl' => $_ENV['GGR_WEBHOOK_URL'], // <----------------------------------------TROCAR URL DO WEBBHOOK
    );

    if ($split != null) {
        $payload['split'] = $split;
    }

    $url = 'https://ws.suitpay.app/api/v1/gateway/request-qrcode';
    $method = 'POST';

    $response = make_request($url, $payload, $method);

    return json_decode($response, true);
}

# check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form = get_form();
    $errors = validate_form($form);

    if (count($errors) > 0) {
        header('Location: ../deposito');
        exit;
    }

    $res = make_pix(
        $form['name'],
        $form['cpf'],
        $form['value']
    );

    if ($res['response'] === 'OK') {
        try {
            // Adiciona a coluna 'data' e obtém a data atual no formato dd/mm/aaaa hh:mm:ss, no horário de Brasília
            $brtTimeZone = new DateTimeZone('America/Sao_Paulo');
            $dateTime = new DateTime('now', $brtTimeZone);
            $userDate = $dateTime->format('d/m/Y H:i');

            $sql = sprintf(
                "INSERT INTO ggr_deposito (email, valor, externalreference, status) VALUES ('%s', '%s', '%s', '%s')",
                $email,
                $form['value'],
                $res['idTransaction'],
                'WAITING_FOR_APPROVAL',
                $userDate
            );

            $conn->query($sql);
        } catch (Exception $ex) {
            var_dump($ex);
            http_response_code(200);
            exit;
        }

        $paymentCode = $res['paymentCode'];
        // Send QR Code to another page
        // var qrCodeUrl = 'pix.php?pix_key=' + encodeURIComponent(data.paymentCode);
        $pixGerado = true;

        $pix_key = $paymentCode;
        $token = $res['idTransaction'];
        /* header("Location: ../GGR/pix.php?pix_key=" . $paymentCode . '&token=' . $res['idTransaction']); */
    } else {
        header('Location: ../GGR');
        exit;
    }
}

$sql = "SELECT deposito_min FROM app LIMIT 1";
$result = $conn->query($sql);

$depositos24 = stmt('SELECT sum(valor) as sum FROM confirmar_deposito WHERE status="PAID_OUT" and data >= DATE_SUB(NOW(), INTERVAL 1 DAY)')['sum'];
$depositosM = stmt('SELECT sum(valor) as sum FROM confirmar_deposito WHERE status="PAID_OUT" and data >= DATE_SUB(NOW(), INTERVAL 1 MONTH)')['sum'];
$depositos = stmt('SELECT depositos FROM app')['depositos'];
$ggr_taxa = stmt('SELECT ggr_taxa FROM ggr')['ggr_taxa'] / 100;

$ggr24 = $depositos24 * $ggr_taxa;
$ggrM = $depositosM * $ggr_taxa;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $depositoMinimo = $row["deposito_min"];
} else {
    $depositoMinimo = 20.00; // Valor padrão caso não seja encontrado no banco
}

?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords" content="Admin Dashboard"/>
    <meta name="description" content="Admin Dashboard"/>
    <meta name="robots" content="noindex,nofollow"/>
    <title>Admin Dashboard</title>

    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/<?php echo $_ENV['LOGOICON_DESENVOLVEDORA'];?>"/>
    <!-- Custom CSS -->
    <link href="../libs/flot/css/float-chart.css" rel="stylesheet"/>
    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet"/>


    <style>
        .text-yellow {
            color: yellow;
            font-size: 25px;


        }

        .text-white2 {
            color: aliceblue;
            font-size: 25px;
        }

        .text-green {
            color: rgb(15, 222, 15);
            font-size: 25px;
        }

        .bold-red {
            color: red;
            font-size: 20px;
        }

        .text-red {
            color: red;
            font-size: 25px;
        }

        .text-red {
            color: red;
            font-size: 25px;
        }


        h1 {
            color: #333;
        }


        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            margin-top: 10px;
            border-radius: 6px;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
        }


        .divqr {
            align-items: center;
            padding: 20px;

            background-color: #ffffff;

        }

        .container3 {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }


        #qr-code-text {
            margin-top: 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            padding: 10px;
            word-break: break-all;
        }

        #qrcode {
            margin-left: 60px;
            padding: 10px;

            border-radius: 10px;
        }


        h4 {
            display: inline-block;
            margin-right: 5px;
            /* Adiciona um espaço entre os elementos, se necessário */
            font-size: 25px;
            color: yellow;
        }

        h5 {
            display: inline-block;
            margin-right: 5px;
            /* Adiciona um espaço entre os elementos, se necessário */
            font-size: 25px;
            color: rgb(255, 255, 255);
        }
    </style>


</head>

<body>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5">
                <a class="navbar-brand" href="../">
                    
                    <span class="logo-text ms-2">
                            <img src="../assets/images/<?php echo $_ENV['LOGOTEXT_DESENVOLVEDORA'];?>" width="150" height="50" alt="homepage"
                                 class="light-logo"/>
                        </span>

                </a>

                <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
            </div>
            <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                <ul class="navbar-nav float-start me-auto">
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)"
                           data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a>
                    </li>


                </ul>
            </div>
        </nav>
    </header>
    <?php include '../components/aside.php' ?>
    <div class="page-wrapper">
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 d-flex no-block align-items-center">

                    <div class="ms-auto text-end">
                        <nav aria-label="breadcrumb">

                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">


            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"></h5>


                        <div class="row">


                          

                          

                            <div class="col-md-6 col-lg-3 col-xlg-3">
                                <div class="card card-hover">
                                    <div class="box bg-dark text-center">
                                        <h1 class="font-light text-white">
                                            <i class="mdi mdi-cash-multiple"></i>
                                        </h1>

                                        <h6 class="text-white">Sua % de GGR</h6>
                                        <h4 class="text-yellow" id="valorUsuarios4"><?= number_format($ggr_taxa * 100, 2, '.', '') ?></h4>
                                        <h4>%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>


                            <div class="row">
                                <div class="col-md-6 col-lg-3 col-xlg-3">
                                    <div class="card card-hover">
                                        <div class="box bg-dark text-center">
                                            <h1 class="font-light text-white">
                                                <i class="mdi mdi-cash-multiple"></i>
                                            </h1>

                                            <h6 class="text-white">GGR em 24H</h6>
                                            <h5>R$</h5>
                                            <h4 class="text-white2" id="valorUsuarios6"><?=number_format($ggr24, 2, '.', '')?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 col-xlg-3">
                                    <div class="card card-hover">
                                        <div class="box bg-dark text-center">
                                            <h1 class="font-light text-white">
                                                <i class="mdi mdi-cash-multiple"></i>
                                            </h1>

                                            <h6 class="text-white">GGR 1M</h6>
                                            <h5>R$</h5>
                                            <h4 class="text-white2" id="valorUsuarios7"><?=number_format($ggrM, 2, '.', '')?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 col-xlg-3">
                                    <div class="card card-hover">
                                        <div class="box bg-dark text-center">
                                            <h1 class="font-light text-white">
                                                <i class="mdi mdi-cash-multiple"></i>
                                            </h1>

                                            <h6 class="text-white">GGR Total</h6>
                                            <h5>R$</h5>
                                            <h4 class="text-white2"
                                                id="valorUsuarios8"><?= number_format((floatval($depositos) * floatval($ggr_taxa)), 2, '.', ''); ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   
                    <?php require '../../components/footer.php'?>
                </div>
                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
                <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
                <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
                <script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
                <script src="../assets/extra-libs/sparkline/sparkline.js"></script>
                <script src="../dist/js/waves.js"></script>
                <script src="../dist/js/sidebarmenu.js"></script>
                <script src="../dist/js/custom.min.js"></script>
</body>

</html>