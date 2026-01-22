<?php
require './../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login");
    echo 'não logado';
    exit();
}

// global $split;
// include './../../split_ggr.php';

$split = array(
    'username' => $_ENV['SUIT_SPLIT'],
    'percentageSplit' => $_ENV['PERCENT_SPLIT']
);

$email = $_SESSION['email'];

include './../connection.php';

$conn = connect();

$sql = "SELECT deposito_min FROM app LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $depositoMinimo = $row["deposito_min"];
} else {
    $depositoMinimo = 20.00; // Valor padrão caso não seja encontrado no banco
}

function get_form()
{
    return array(
        'name' => 'edito silva',
        'cpf' => $_POST['document'],
        'value' => $_POST['valor_transacao'],
    );
}

//
function validate_form($form)
{
    global $depositoMinimo;

    $errors = array();


    if (empty($form['cpf'])) {
        $errors = ['O CPF é obrigatório'];
    }

    if (empty($form['value'])) {
        $errors = ['O valor é obrigatório'];
    }

    if ($form['value'] < $depositoMinimo) {
        $errors = ['O valor mínimo é de R$ ' . $depositoMinimo];
    }

    return $errors;
}

//qyM8zsh-gdcE8NU-Voaq5pi-opXBuQ
//F6OlPruAw0-hqjxEmyiK9-vkxaZucM88-oI9W3H86TL-1s7f1n9tp0-2574e
//https://subwaypix.co/webhook



// function make_request($url, $payload, $method = 'POST')
// {
//     $headers = array(
//         "Content-Type: application/json",
//         "ci: " . $_ENV['SUIT_PAY_CI'],
//         "cs: " . $_ENV['SUIT_PAY_CS']
//     );
    
//     $ch = curl_init($url);

//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//     $result = curl_exec($ch);

//     curl_close($ch);
//     return $result;
// }

function make_request($url, $urlPath, $payload)
{
    $publicKey = 'qyM8zsh-gdcE8NU-Voaq5pi-opXBuQ';
    $secretKey = 'F6OlPruAw0-hqjxEmyiK9-vkxaZucM88-oI9W3H86TL-1s7f1n9tp0-2574e';

    $bodyString = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $baseString = $bodyString . '&|&' . $urlPath;

    $hashHex = hash_hmac('sha256', $baseString, $secretKey);
    $signature = rtrim(base64_encode($hashHex), '=');

    $headers = [
        "Content-Type: application/json",
        "x-api-key: $publicKey",
        "x-api-signature: $signature"
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $bodyString,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30
    ]);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('Erro cURL: ' . curl_error($ch));
    }

    curl_close($ch);
    return json_decode($result, true);
}

function make_pix($name, $cpf, $value)
{
    $baseUrl = 'https://system.horizonbanking.com.br';
    $urlPath = '/api/orders/create_simple';
    $url     = $baseUrl . $urlPath;

    $externalId = uniqid('dep-');

    $payload = [
        'customId'  => $externalId,
        'amount'    => floatval($value),
        'returnUrl' => '',
        'type'      => 'pix',
        'customer'  => [
            'name'     => $name,
            'email'    => 'cliente@email.com',
            'document' => $cpf,
        ],
        'paymentMethodForm' => [
            'docType'    => 'CPF',
            'document'   => $cpf,
            'email'      => 'edito.desenvolvedor@gmail.com',
            'fullName'   => $name,
            'clientCode' => 'CUST-00015',
            'phone'      => '+5511918689508',
        ]
    ];

    $response = make_request($url, $urlPath, $payload);

    $qrCode = $response['data']['order']['invoice']['bankData']['payment']['qrCode'] ?? null;

    if (!$qrCode) {
        throw new Exception('QR Code Pix não encontrado');
    }

    return [
        'response'      => 'OK',
        'idTransaction' => $externalId,
        'paymentCode'   => $qrCode
    ];
}


// function make_pix($name, $cpf, $value)
// {
//     global $split;
//     $dueDate = date('Y-m-d', strtotime('+1 day'));
//     $email = 'cliente@email.com';

//     $payload = array(
//         'requestNumber' => '12356',
//         'dueDate' => $dueDate,
//         'amount' => floatval($value),
//         'client' => array(
//             'name' => $name,
//             'email' => $email,
//             'document' => $cpf,
//         ),
//         'callbackUrl' => $_ENV['DEPOSIT_WEBHOOK_URL']
//     );

//     if ($split != null) {
//         $payload['split'] = $split;
//     }

//     $url = 'https://ws.suitpay.app/api/v1/gateway/request-qrcode';
//     $method = 'POST';

//     $response = make_request($url, $payload, $method);

//     return json_decode($response, true);
// }

# check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form = get_form();

    $errors = validate_form($form);

    if (count($errors) > 0) {
        header('Location: ../deposito');
        $_SESSION['errors'] = $errors;
        exit;
    }

    $res = make_pix(
        $form['name'],
        $form['cpf'],
        $form['value']
    );

    if ($res['response'] === 'OK') {
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        try {
            $sql = sprintf(
                "INSERT INTO confirmar_deposito (email, valor, externalreference, status, bonus) VALUES ('%s', '%s', '%s', '%s', '%s')",
                $email,
                $form['value'],
                $res['idTransaction'],
                'WAITING_FOR_APPROVAL',
                $_POST['bonus']
            );

            $conn->query($sql);
            $conn->close();
        } catch (Exception $ex) {
            var_dump($ex);
            http_response_code(200);
            exit;
        }

        $paymentCode = $res['paymentCode'];
        // Send QR Code to another page
        // var qrCodeUrl = 'pix.php?pix_key=' + encodeURIComponent(data.paymentCode);
        header("Location: ../deposito/pix.php?pix_key=" . $paymentCode . '&token=' . $res['idTransaction'] . '&value=' . $form['value']);
    } else {
        header('Location: ../deposito');
    }
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>


<!DOCTYPE html>

<html lang="pt-br" class="w-mod-js w-mod-ix wf-spacemono-n4-active wf-spacemono-n7-active wf-active">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        #hero {
            background-image: url('../_next/background.png') !important;
            background-size: cover;
            background-color: #ffffff;
        }

        .wf-force-outline-none[tabindex="-1"]:focus {
            outline: none;
        }
    </style>
    <meta charset="pt-br">
    <title><?php echo $_ENV['NOME_SITE']; ?> 🌊 </title>

    <meta property="og:image" content="../img/logo.png">

    <meta content="<?php echo $_ENV['NOME_SITE']; ?> 🌊" property="og:title">

    <?php require '../components/disable.php'; ?>
    <meta name="twitter:image" content="../img/logo.png">
    <meta content="<?php echo $_ENV['NOME_SITE']; ?> 🌊" property="twitter:title">
    <meta property="og:type" content="website">
    <meta content="summary_large_image" name="twitter:card">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="arquivos/page.css" rel="stylesheet" type="text/css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        !function (o, c) {
            var n = c.documentElement,
                t = " w-mod-";
            n.className += t + "js", ("ontouchstart" in o || o.DocumentTouch && c instanceof DocumentTouch) && (n
                .className += t + "touch")
        }(window, document);
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/logo.png">
    <link rel="icon" type="image/x-icon" href="../img/logo.png">
    <link rel="stylesheet" href="arquivos/css" media="all">

    <?php
    if (stmt("SELECT count(*) as count from pixels WHERE local='header' AND (pagina='deposito' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1) {
        $pixel = stmt("SELECT * from pixels WHERE local='header' AND (pagina='deposito' OR pagina='todas' OR pagina='todo-sem-jogo')");
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    } else {
        foreach (stmt("SELECT * from pixels WHERE local='header' AND (pagina='deposito' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) {
            echo file_get_contents('../uploads/pixels/' . $pixel['script']);
        }
    }
    ?>
</head>

<body>
<div class="elementor-element elementor-element-8ae2ec4 e-con-boxed e-con" data-id="8ae2ec4"
     data-element_type="container" data-settings="{" content_width
":"boxed"}"="">
<div class="e-con-inner">
    <div class="elementor-element elementor-element-64c1a37 elementor-widget elementor-widget-html" data-id="64c1a37"
         data-element_type="widget" data-widget_type="html.default">
        <div class="elementor-widget-container">
            <div class="elementor-element elementor-element-5e3d6ce elementor-widget elementor-widget-html"
                 data-id="5e3d6ce" data-element_type="widget" data-widget_type="html.default">
                <div class="elementor-widget-container">
                    <script src="https://cdn.jsdelivr.net/npm/notiflix@2.6.0/dist/notiflix-aio-2.6.0.min.js"></script>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php
if (stmt("SELECT count(*) as count from pixels WHERE local='body' AND (pagina='deposito' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1) {
    $pixel = stmt("SELECT * from pixels WHERE local='body' AND (pagina='deposito' OR pagina='todas' OR pagina='todo-sem-jogo')");
    echo file_get_contents('../uploads/pixels/' . $pixel['script']);
} else {
    foreach (stmt("SELECT * from pixels WHERE local='body' AND (pagina='deposito' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) {
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    }
}
?>
<div>
    <div data-collapse="small" data-animation="default" data-duration="400" role="banner" class="navbar w-nav">
        <div class="container w-container">

            <a href="/painel" aria-current="page" class="brand w-nav-brand" aria-label="home">

                <img src="arquivos/l2.png" loading="lazy" height="28" alt="" class="image-6">

                <div class="nav-link logo"><?php echo $_ENV['NOME_SITE']; ?></div>
            </a>
            <nav role="navigation" class="nav-menu w-nav-menu">
                <a href="../painel" class="nav-link w-nav-link" style="max-width: 940px;">Jogar</a>

                <a href="../saque/" class="nav-link w-nav-link" style="max-width: 940px;">Saque</a>

                <a href="../afiliate/" class="nav-link w-nav-link" style="max-width: 940px;">Indique e Ganhe</a>

                <a href="../logout.php" class="nav-link w-nav-link" style="max-width: 940px;">Sair</a>
                <a href="../deposito/" class="button nav w-button w--current">DEPOSITAR</a>
            </nav>


            <style>
                .buttonBonusText {
                    color: yellow;
                    font-size: 1rem;
                    text-align: center;
                    font-weight: bold;
                    margin: 0 !important;
                }

                .buttonDepositoText {
                    margin-bottom: 0 !important;
                    color: #ffffff !important;
                    font-size: 1rem;
                    font-weight: bold;
                    text-align: center;
                }

                .buttonBonus {
                    margin: 0.5vw 0;
                    text-align: start;
                    width: 12rem;
                }

                .buttonBonusSelected {
                    background-color: #5217FF !important;
                }

                .containerButtonBonus {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: wrap;
                    width: 60%;
                    margin: 1.5vw auto;
                    justify-content: space-around;
                }

                @media screen and (max-width: 599px) {
                    .containerButtonBonus {
                        display: flex;
                        flex-direction: row;
                        flex-wrap: wrap;
                        width: 100%;
                        margin: 1vw auto 2vw auto;
                        justify-content: space-around;
                    }

                    .buttonBonus {
                        margin: 0.5vw 0;
                        text-align: center;
                        width: 100%;
                    }

                    .buttonBonusText {
                        color: yellow;
                        margin-bottom: 0 !important;
                        font-size: 1.2rem;
                        font-weight: bold;
                        margin: 0 !important;
                    }

                    .buttonDepositoText {
                        margin-bottom: 0 !important;
                        color: #000000;
                        font-size: 1.2rem;
                        font-weight: bold;
                        text-align: center;
                    }
                }

                .nav-bar a {
                    color: white;
                    text-decoration: none;
                    padding: 10px;

                    display: block;
                    margin-bottom: 10px;
                }

                .nav-bar a.login {
                    color: white;
                    /* Cor do texto para o botão Login */
                }

                @keyframes pulse {
                    0% {
                        transform: scale(1);
                    }
                    50% {
                        transform: scale(1.1);
                    }
                    100% {
                        transform: scale(1);
                    }
                }

                .pulsing {
                    animation: pulse 1s infinite;
                }

                .deposit-form {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                }

            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var menuButton = document.querySelector('.menu-button');
                    var navBar = document.querySelector('.nav-bar');

                    menuButton.addEventListener('click', function () {
                        // Toggle the visibility of the navigation bar
                        if (navBar.style.display === 'block') {
                            navBar.style.display = 'none';
                        } else {
                            navBar.style.display = 'block';
                        }
                    });
                });
            </script>


            <div class="w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button" tabindex="0"
                 aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">

            </div>
            <div class="menu-button w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button"
                 tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                <div class="icon w-icon-nav-menu"></div>
            </div>
        </div>
        <div class="w-nav-overlay" data-wf-ignore="" id="w-nav-overlay-0"></div>
    </div>
    <div class="nav-bar">
        <a href="../painel/" class="button w-button w--current">
            <div>Jogar</div>
        </a>
        <a href="../saque/" class="button w-button w--current">
            <div>Saque</div>
        </a>

        </a>
        <a href="../afiliate/" class="button w-button w--current">
            <div>Indique & Ganhe</div>
        </a>
        <a href="../logout.php" class="button w-button w--current">
            <div>Sair</div>
        </a>
        <a href="../deposito/" class="button w-button w--current">DEPOSITAR</a>
    </div>

    <section id="hero" class="hero-section dark wf-section">
        <div class="minting-container w-container">
            <img src="arquivos/asset-deposit.webp" loading="lazy" width="240"
                 data-w-id="6449f730-ebd9-23f2-b6ad-c6fbce8937f7" alt="Roboto #6340" class="mint-card-image">
            <h2>Depósito</h2>
            <p>PIX: depósitos instantâneos com uma pitada de diversão e muita praticidade. <br>
            </p>

            <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
            <form action="<?= $_ENV['BASE_URL'] ?>/deposito/index.php" method="POST" class="deposit-form">
                <div class="properties">
                    <h4 class="rarity-heading">CPF</h4>
                    <div class="rarity-row roboto-type2">
                        <input class="large-input-field w-input" maxlength="11" placeholder="Seu número de CPF"
                               type="text" id="document" name="document" required>
                    </div>
                    <h4 class="rarity-heading">VALOR</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="text" class="large-input-field w-input money-mask" maxlength="256"
                               name="valor_transacao" id="valuedeposit"
                               placeholder="Depósito mínimo de R$<?php echo number_format($depositoMinimo, 2, ',', ''); ?>"
                               required min="<?php echo $depositoMinimo; ?>">
                    </div>
                    <div class="rarity-row roboto-type2"
                         style="text-align: center; background-color: yellow; border: 2px dotted black;">
                        ÚLTIMO DIA PARA APROVEITAR O DEPÓSITO EM DOBRO.<br>
                        DISPONÍVEL SOMENTE ATÉ ÀS 23:59H DE HOJE!!!<br>
                    </div>
                    <input type="hidden" id="bonus" name="bonus" value="0">
                </div>
                <input type="hidden" name="valor_transacao_session"
                       value="<?php echo isset($_SESSION['valor_transacao']) ? $_SESSION['valor_transacao'] : ''; ?>">
                <?php require '../adm/components/messages.php'; ?>
                <div class="containerButtonBonus button-container">
                    <?php foreach (get_all('bonus') as $bonus) { ?>
                        <button type="button" class="buttonBonus button nav w-button"
                                onclick="updateValue(this, <?php echo $bonus['deposito'] ?>, <?php echo $bonus['ganho'] ?>)">
                            <p class="buttonDepositoText">
                                R$<?php echo number_format($bonus['deposito'], 2, ',', '') ?>
                            </p>
                            <p class="buttonBonusText">
                                <?php if ($bonus['ganho'] == 0) { ?>
                                    (SEM BÔNUS)
                                <?php } else { ?>
                                    (GANHE R$<?php echo number_format($bonus['deposito'] + $bonus['ganho'], 2, ',', '') ?>)
                                <?php } ?>
                            </p>
                        </button>
                    <?php } ?>
                </div>
                <script>
                    let last_fire = null

                    function validate_deposit(value) {
                        if (value === '' || value == null) {
                            return;
                        }

                        var valueMin = <?php echo $depositoMinimo; ?>;

                        if (value < valueMin) {
                            Swal.fire({
                                title: 'Ops!',
                                text: 'O valor mínimo para depósito é de R$' + valueMin,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return;
                        }

                        // Remove a classe de todos os botões
                        var buttons = document.querySelectorAll('.buttonBonus');

                        buttons.forEach(function (btn) {
                            btn.classList.remove('buttonBonusSelected');
                        });

                        // Adiciona a classe apenas ao botão clicado
                        document.getElementById('valuedeposit').value = value;
                    }

                    function fire_input_validation() {
                        if (last_fire) {
                            clearTimeout(last_fire);
                        }

                        last_fire = setTimeout(function () {
                            validate_deposit($('#valuedeposit').val());
                            last_fire = null;
                        }, 1000);
                    }

                    $('#valuedeposit').on('input', function () {
                        fire_input_validation();

                        $('.buttonBonus').removeClass('buttonBonusSelected');
                        $('#bonus').val(0);
                    });


                    function updateValue(button, value, value2) {
                        const valueMin = <?php echo $depositoMinimo; ?>;

                        if (value < valueMin) {
                            Swal.fire({
                                title: 'Ops!',
                                text: 'O valor mínimo para depósito é de R$' + valueMin,
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                            return;
                        }

                        $('.buttonBonus').removeClass('buttonBonusSelected');

                        button.classList.add('buttonBonusSelected');

                        // Atualiza os valores conforme necessário
                        $('#valuedeposit').val(value);
                        $('#bonus').val(value2);
                        $('#submitButton').addClass('pulsing')
                    }
                </script>
                <input type="submit" id="submitButton" name="gerar_qr_code" value="Depositar via PIX"
                       class="primary-button w-button">
            </form>
        </div>
    </section>
    <div class="intermission wf-section"></div>
    <div id="about" class="comic-book white wf-section">
        <div class="minting-container left w-container">
            <div class="w-layout-grid grid-2">
                <img src="arquivos/money.png" loading="lazy" width="240" alt="Roboto #6340" class="mint-card-image v2">
                <div>
                    <h2>Indique um amigo e ganhe R$ no PIX</h2>
                    <h3>Como funciona?</h3>
                    <p>Convide seus amigos que ainda não estão na plataforma. Você receberá R$15 por cada amigo que
                        se
                        inscrever e fizer um depósito. Não há limite para quantos amigos você pode convidar. Isso
                        significa que também não há limite para quanto você pode ganhar!</p>
                    <h3>Como recebo o dinheiro?</h3>
                    <p>O saldo é adicionado diretamente ao seu saldo no painel abaixo, com o qual você pode sacar
                        via
                        PIX.</p>

                </div>
            </div>
        </div>
    </div>
    <div class="footer-section wf-section">
        <div class="domo-text">SUBWAY <br>
        </div>
        <div class="domo-text purple">SURFERS <br>
        </div>
        <div class="follow-test">© Copyright Postbrands Limited, with registered offices at Dr. M.L. King Boulevard 117,
            accredited by license GLH-16286002012.<a/></a> </div>
        <div class="follow-test">
            <a href="#">
                <strong class="bold-white-link">Termos de uso</strong>
            </a>
        </div>
        <div class="follow-test">contato@subwaysurf.com</div>

    </div>


</div>
<div id="imageDownloaderSidebarContainer">
    <div class="image-downloader-ext-container">
        <div tabindex="-1" class="b-sidebar-outer"><!---->
            <div id="image-downloader-sidebar" tabindex="-1" role="dialog" aria-modal="false" aria-hidden="true"
                 class="b-sidebar shadow b-sidebar-right bg-light text-dark" style="width: 500px; display: none;">
                <!---->
                <div class="b-sidebar-body">
                    <div></div>
                </div><!---->
            </div><!----><!---->
        </div>
    </div>
</div>
<div style="visibility: visible;">
    <div></div>
    <div>
        <div style="display: flex; flex-direction: column; z-index: 999999; bottom: 88px; position: fixed; right: 16px; direction: ltr; align-items: end; gap: 8px;">
            <div style="display: flex; gap: 8px;"></div>
        </div>
        <style>
            @-webkit-keyframes ww-0733d640-bd43-40f6-a8a7-7e086fc12b92-launcherOnOpen {
                0% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }

                30% {
                    -webkit-transform: translateY(-5px) rotate(2deg);
                    transform: translateY(-5px) rotate(2deg);
                }

                60% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }


                90% {
                    -webkit-transform: translateY(-1px) rotate(0deg);
                    transform: translateY(-1px) rotate(0deg);

                }

                100% {
                    -webkit-transform: translateY(-0px) rotate(0deg);
                    transform: translateY(-0px) rotate(0deg);
                }
            }

            @keyframes ww-0733d640-bd43-40f6-a8a7-7e086fc12b92-launcherOnOpen {
                0% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }

                30% {
                    -webkit-transform: translateY(-5px) rotate(2deg);
                    transform: translateY(-5px) rotate(2deg);
                }

                60% {
                    -webkit-transform: translateY(0px) rotate(0deg);
                    transform: translateY(0px) rotate(0deg);
                }


                90% {
                    -webkit-transform: translateY(-1px) rotate(0deg);
                    transform: translateY(-1px) rotate(0deg);

                }

                100% {
                    -webkit-transform: translateY(-0px) rotate(0deg);
                    transform: translateY(-0px) rotate(0deg);
                }
            }

            @keyframes ww-0733d640-bd43-40f6-a8a7-7e086fc12b92-widgetOnLoad {
                0% {
                    opacity: 0;
                }

                100% {
                    opacity: 1;
                }
            }

            @-webkit-keyframes ww-0733d640-bd43-40f6-a8a7-7e086fc12b92-widgetOnLoad {
                0% {
                    opacity: 0;
                }

                100% {
                    opacity: 1;
                }
            }
        </style>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const urlParams = new URLSearchParams(window.location.search);
        const msg = urlParams.get('msg');
        const type = urlParams.get('type');
        const acumulado = urlParams.get('val')

        if (msg && type == 'win') {
            Swal.fire({
                title: 'VAMOS JOGAR COM DINHEIRO REAL?',
                html: 'Parabéns! Você poderia ter ganhado<br/> <strong style="color: #11d97f;">R$ ' + acumulado + '</strong>. <br>  Para sacar dinheiro real você precisa depositar um valor mínimo. #ficadica! <br>  <br>⬇️ Clique no Botão Abaixo',
                icon: 'success', // 'success', 'error', 'warning', 'info', 'question'
                // Você pode adicionar outras opções do SweetAlert aqui
            });
        } else if (msg == 'gameover') {
            Swal.fire({
                title: 'VAMOS JOGAR COM DINHEIRO REAL?',
                html: 'Para sacar dinheiro real você precisa  depositar um valor mínimo. #ficadica! <br>  <br>⬇️ Clique no Botão Abaixo',
                icon: 'error', // 'success', 'error', 'warning', 'info', 'question'
                // Você pode adicionar outras opções do SweetAlert aqui
            });
        }
    });
</script>

<script>
    var position = "left-bottom"; // Posição da notificação na tela
    var animation = "from-left"; // Animação da notificação
    var timeout = 4000; // Tempo que a notificação fica visível na tela

    // Arrays com os nomes dos clientes e os pacotes/reservas
    var notifications = [
        '<strong>Adriana</strong> Acabou de Sacar <strong>R$ 160.00 </strong>',
        '<strong>Marcelo</strong> Acabou de Sacar <strong>R$ 150.00 </strong>',
        '<strong>Patricia</strong> Acabou de Sacar <strong>R$ 150.00 </strong>',
        '<strong>Carlos</strong> Acabou de Sacar <strong>R$ 130,00 </strong>',
        '<strong>Luiza</strong> Acabou de Sacar <strong>R$ 165,00 </strong>',
        '<strong>Fabricio</strong> Acabou de Sacar <strong>R$ 125,00 </strong>',
        '<strong>Matheus</strong> Acabou de Sacar <strong>R$ 178,00 </strong>',
        '<strong>Geovane</strong> Acabou de Sacar <strong>R$ 11120,00 </strong>',
        '<strong>Lia</strong> Acabou de Sacar <strong>R$ 175,00 </strong>',
        '<strong>Isabela</strong> Acabou de Sacar <strong>R$ 145,00 </strong>',
        '<strong>Marcio</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Maria</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Felipe</strong> Acabou de Sacar <strong>R$ 167,00 </strong>',
        '<strong>Geovane</strong> Acabou de Sacar <strong>R$ 175,00 </strong>',
        '<strong>Dávila</strong> Acabou de Sacar <strong>R$ 130,00 </strong>',
        '<strong>Levi</strong> Acabou de Sacar <strong>R$ 150.00 </strong>',
        '<strong>Enzo</strong> Acabou de Sacar <strong>R$ 165,00 </strong>',
        '<strong>Ravi</strong> Acabou de Sacar <strong>R$ 125,00 </strong>',
        '<strong>Aline</strong> Acabou de Sacar <strong>R$ 178,00 </strong>',
        '<strong>Jéssica</strong> Acabou de Sacar <strong>R$ 145,00 </strong>',
        '<strong>Leticia</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Antonela</strong> Acabou de Sacar <strong>R$ 13760,00 </strong>',
        '<strong>Babi</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Renan</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Wesley</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Thalysson</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Thay</strong> Acabou de Sacar <strong>R$ 617,00 </strong>',
        '<strong>Lira</strong> Acabou de Sacar <strong>R$ 162,00 </strong>',
        '<strong>Cefas</strong> Acabou de Sacar <strong>R$ 167,00 </strong>',
        '<strong>Tom</strong> Acabou de Sacar <strong>R$ 132,00 </strong>',
        '<strong>Rodrigo</strong> Acabou de Sacar <strong>R$ 167,00 </strong>',
        '<strong>Yuri</strong> Acabou de Sacar <strong>R$ 147,00 </strong>',
        '<strong>Dyno</strong> Acabou de Sacar <strong>R$ 135,00 </strong>',
        '<strong>Caio</strong> Acabou de Sacar <strong>R$ 125,00 </strong>',

    ];

    var option = {
        position: position,
        cssAnimationStyle: animation,
        plainText: false,
        timeout: timeout
    };

    function show_notification() {
        var notification = notifications[Math.floor(Math.random() * notifications.length)];

        Notiflix.Notify.Success(notification, option);

        setTimeout(show_notification, 8000);
    }

    setTimeout(show_notification, 8000);
</script>
</body>

</html>