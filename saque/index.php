<?php

require './../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable('./../');
$dotenv->load();
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../");
    exit();
}

include './../connection.php';

$conn = connect();

$mensagem_saque_ok = "";
$mensagem_saque_erro = "";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

$getLinkQuery = "SELECT saques_min FROM app";
$stmt = $conn->prepare($getLinkQuery);

if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$executed = $stmt->execute();

if ($executed === false) {
    die('execute() failed: ' . htmlspecialchars($stmt->error));
}

$stmt->store_result();
$stmt->bind_result($saques_min);
$stmt->fetch();
$stmt->close();

$getLinkQuery = "SELECT cpa FROM app";
$stmt = $conn->prepare($getLinkQuery);
$stmt->execute();
$stmt->bind_result($cpa);
$stmt->fetch();
$stmt->close();

$getLinkQuery = "SELECT cpa FROM appconfig WHERE email = ?";
$stmt = $conn->prepare($getLinkQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($cpa_u);
$stmt->fetch();
$stmt->close();

if ($cpa_u != 0) {
    $cpa = $cpa_u;
}

$getLinkQuery = "SELECT saldo, rollover, rollover_total FROM appconfig WHERE email = ?";
$stmt = $conn->prepare($getLinkQuery);

if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $email);
$executed = $stmt->execute();

if ($executed === false) {
    die('execute() failed: ' . htmlspecialchars($stmt->error));
}

$stmt->store_result();
$stmt->bind_result($saldo, $rollover, $rollover_total);
$stmt->fetch();
$stmt->close();

$getLinkQuery = "SELECT saques_max FROM app";
$stmt = $conn->prepare($getLinkQuery);

if ($stmt === false) {
    die('prepare() failed: ' . htmlspecialchars($conn->error));
}

$executed = $stmt->execute();

if ($executed === false) {
    die('execute() failed: ' . htmlspecialchars($stmt->error));
}

$stmt->store_result();
$stmt->bind_result($saques_max);
$stmt->fetch();
$stmt->close();

$desabilitarPix = "";
$text_disabled = "";
if ($saldo < $saques_min || $rollover > 0) {
    $desabilitarPix = "disabled";
    $text_disabled = "Não possui o valor minimo";
}

$conn->close();

if ($rollover <= 0) {
    $pode_sacar = true;
} else {
    $pode_sacar = false;
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
    <?php require '../components/disable.php'; ?>
    <meta property="og:image" content="../img/logo.png">

    <meta content="SubwayPay 🌊" property="og:title">
    <meta name="twitter:site" content="@subwaypay">
    <meta name="twitter:image" content="../img/logo.png">
    <meta property="og:type" content="website">

    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="arquivos/page.css" rel="stylesheet" type="text/css">
    <script src="arquivos/webfont.js" type="text/javascript"></script>

    <script type="text/javascript">
        WebFont.load({
            google: {
                families: ["Space Mono:regular,700"]
            }
        });
    </script>


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
    if (stmt("SELECT count(*) as count from pixels WHERE local='header' AND (pagina='saque' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1) {
        $pixel = stmt("SELECT * from pixels WHERE local='header' AND (pagina='saque' OR pagina='todas' OR pagina='todo-sem-jogo')");
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    } else {
        foreach (stmt("SELECT * from pixels WHERE local='header' AND (pagina='saque' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) {
            echo file_get_contents('../uploads/pixels/' . $pixel['script']);
        }
    };
    ?>
</head>

<body>
<?php
if (stmt("SELECT count(*) as count from pixels WHERE local='body' AND (pagina='saque' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1) {
    $pixel = stmt("SELECT * from pixels WHERE local='body' AND (pagina='saque' OR pagina='todas' OR pagina='todo-sem-jogo')");
    echo file_get_contents('../uploads/pixels/' . $pixel['script']);
} else {
    foreach (stmt("SELECT * from pixels WHERE local='body' AND (pagina='saque' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) {
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    }
};
?>
<div>
    <div data-collapse="small" data-animation="default" data-duration="400" role="banner" class="navbar w-nav"
         id='main_nav'>
        <div class="container w-container" id="nav_menu">
            <a href="/painel" aria-current="page" class="brand w-nav-brand" aria-label="home">
                <img src="arquivos/l2.png" loading="lazy" height="28" alt="" class="image-6">
                <div class="nav-link logo"><?php echo $_ENV['NOME_SITE']; ?></div>
            </a>
            <nav role="navigation" class="nav-menu w-nav-menu">
                <a href="../painel/" class="nav-link w-nav-link" style="max-width: 940px;">Jogar</a>
                <a href="../saque/" class="nav-link w-nav-link w--current" style="max-width: 940px;">Saque</a>

                <a href="../afiliate" class="nav-link w-nav-link" style="max-width: 940px;">Indique e Ganhe</a>


                <a href="../logout.php" class="nav-link w-nav-link" style="max-width: 940px;">Sair</a>
                <a href="../deposito/" class="button nav w-button" id="deposit-btn">DEPOSITAR</a>
            </nav>


            <style>
                #deposit-btn {
                    display: inline-block;
                    padding: 9px 15px;
                    background-color: #3898ec;
                    color: #fff;
                    border: 0;
                    line-height: inherit;
                    text-decoration: none;
                    cursor: pointer;
                    border-radius: 8px;
                }

                #deposit-btn:hover {
                    background-color: #5100ff;
                }

                .nav-bar {
                    display: none;
                    background-color: #333;
                    /* Cor de fundo do menu */
                    padding: 20px;
                    /* Espaçamento interno do menu */
                    width: 90%;
                    /* Largura total do menu */

                    position: fixed;
                    /* Fixa o menu na parte superior */
                    top: 0;
                    left: 0;
                    z-index: 1000;
                    /* Garante que o menu está acima de outros elementos */
                }

                .nav-bar a {
                    color: white;
                    /* Cor dos links no menu */
                    text-decoration: none;
                    padding: 10px;
                    /* Espaçamento interno dos itens do menu */
                    display: block;
                    margin-bottom: 10px;
                    /* Espaçamento entre os itens do menu */
                }

                .nav-bar a.login {
                    color: white;
                    /* Cor do texto para o botão Login */
                }

                .button.w-button {
                    text-align: center;
                }

                .alert-danger {
                    color: #721c24;
                    background-color: #f8d7da;
                    border-color: #f5c6cb;
                }

                .alert {
                    position: relative;
                    padding: .75rem 1.25rem;
                    margin-bottom: 1rem;
                    border: 1px solid transparent;
                    border-radius: .25rem;
                }

                .dataTables_filter > label {
                    display: flex;
                    align-items: center;
                    gap: 10px
                }

                .dataTables_filter > label > button {
                    margin-left: 10px;
                    background-color: #4CAF50;
                    border: none;
                    color: white;
                    padding: 4px 10px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                }

                #user-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-family: Arial, sans-serif;
                }

                #user-table thead {
                    background-color: #4CAF50;
                    color: white;
                }

                #user-table thead th {
                    padding: 10px;
                    text-align: left;
                }

                #user-table tbody {
                    background-color: #f2f2f2;
                }

                #user-table tbody td {
                    padding: 10px;
                }

                #user-table tbody tr:hover {
                    background-color: #ddd;
                }

                #user-table {
                    border: 1px solid #ddd;
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

                document.addEventListener('DOMContentLoaded', (event) => {
                    var saldo = parseFloat("<?php echo $saldo; ?>");
                    var desabilitarPix = "<?php echo $desabilitarPix; ?>";
                    console.log(desabilitarPix);
                });

                const rolloverAtual = <?php echo isset($rollover) ? floatval($rollover) : 0; ?>;
            </script>


            <style>
                .menu-button2 {
                    border-radius: 15px;
                    background-color: #000;
                }

                .divDisabledPix {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }

                .divBotaoPix {
                    margin: 1vw auto 0 auto;
                    font-size: 1.25rem;
                }

                .text-red {
                    color: red;
                }

                .observacao {
                    background-color: yellow;
                    padding: 0 2vw;
                }

                .divInfos {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                }
            </style>


            <div class="w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button" tabindex="0"
                 aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                <div class="" style="-webkit-user-select: text;">


                    <a href="../deposito/" class="menu-button2 w-nav-dep nav w-button">DEPOSITAR</a>
                </div>
            </div>
            <div class="menu-button w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button"
                 tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                <div class="icon w-icon-nav-menu"></div>
            </div>
        </div>
        <div class="w-nav-overlay" data-wf-ignore="" id="w-nav-overlay-0"></div>
    </div>
    <div class="nav-bar">
        <a href="../painel/" class="button w-button">
            <div>Jogar</div>
        </a>
        <a href="../saque/" class="button w-button">
            <div>Saque</div>
        </a>

        <a href="../afiliate/" class="button w-button">
            <div>Indique & ganhe</div>
        </a>
        <a href="../logout.php" class="button w-button">
            <div>Sair</div>
        </a>
        <a href="../deposito/" class="button w-button">DEPOSITAR</a>
    </div>


    <section id="hero" class="hero-section dark wf-section">
        <div class="minting-container w-container">
            <img src="arquivos/with.gif" loading="lazy" width="240" data-w-id="6449f730-ebd9-23f2-b6ad-c6fbce8937f7"
                 alt="Roboto #6340" class="mint-card-image">
            <h2>Saque</h2>
            <p>PIX: saques instantâneos com muita praticidade. <br>
            </p>
            <form action="<?=$_ENV['BASE_URL']?>/saque/processar_saque.php" data-name="" id="payment_pix" name="payment_pix" method="post"
                  aria-label="Form">
                <div class="properties">
                    <h4 class="rarity-heading">Nome do destinatário:</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="text"
                               class="large-input-field w-node-_050dfc36-93a8-d840-d215-4fca9adfe60d-9adfe605 w-input"
                               maxlength="256" name="withdrawName" placeholder="Nome do Destinatario" id="withdrawName"
                               required="">
                    </div>
                    <h4 class="rarity-heading">Chave PIX CPF:</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="text"
                               class="large-input-field w-node-_050dfc36-93a8-d840-d215-4fca9adfe60d-9adfe605 w-input centered-input"
                               maxlength="256" name="withdrawCPF" placeholder="Seu número de CPF" id="withdrawCPF"
                               required="">
                    </div>
                    <h4 class=" rarity-heading">Valor para saque</h4>
                    <div class="rarity-row roboto-type2">
                        <input type="number" data-name="Valor de saque" name="withdrawValue" id="withdrawValue"
                               class="large-input-field w-node-_050dfc36-93a8-d840-d215-4fca9adfe60d-9adfe605 w-input centered-input"
                               placeholder="Seu saldo atual é: R$<?= $saldo ?>" min="<?= $saques_min ?>"
                               max="<?= $saques_max ?>">
                    </div>
                    <div class="divDisabledPix">
                        <input type="submit" value="Sacar via PIX" id="pixgenerator" <?php //echo $desabilitarPix; ?>
                               class="divBotaoPix primary-button w-button"><br><br>
                        <?php if (isset($_SESSION['errors'])) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php foreach ($_SESSION['errors'] as $error) { ?>
                                    <p><?php echo $error ?></p>
                                <?php } ?>
                            </div>
                            <?php unset($_SESSION['errors']); ?>
                        <?php } ?>
                        <div>
                            <!---<p class="text-red">* O valor deve ser igual ou maior ao valor <b>MÍNiMO</b> de saque que é atualmente: <?php echo $saques_min ?></p>
                                <p class="text-red">* O valor deve ser igual ou menor ao valor <b>MÁXIMO</b> de saque que é atualmente: <?php echo $saques_max ?></p>-->

                        </div>
                        <div class="divInfos">
                            <!--<p>Saldo Real: <b>R$<?php echo number_format($saldo, 2, '.', ''); ?></b></p>-->
                            <p>Saldo Disponível para Saque:
                                <b>R$<?php echo number_format(($pode_sacar ? $saldo : 0), 2, '.', ''); ?></b></p>
                            <p>Débito Rollover: <b
                                        class='text-red'>R$<?php echo number_format($rollover, 2, '.', ''); ?></b></p>
                            <p class='observacao'>Obs: Você só pode sacar o seu saldo quando o ROLLOVER for igual a
                                <b>0</b></p>
                            <!--<p>O Valor Mínimo para Saques é: <b>R$<?php echo number_format($saques_min, 2, '.', ''); ?></b></p>-->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <div class="intermission wf-section"></div>
    <div id="rarity" class="rarity-section wf-section">
        <div class="minting-container w-container">
            <img src="arquivos/money-cash.gif" loading="lazy" width="240" alt="Robopet 6340" class="mint-card-image">
            <h2>Histórico financeiro</h2>
            <p class="paragraph">As retiradas para sua conta bancária são processadas pelo setor financeiro.
                <br>
            </p>
            <div class="properties">
                <h3 class="rarity-heading">Saques realizados</h3>
            </div>
            <div class="table-responsive">
                <?php $saques = get_all('saques', ['email' => $_SESSION['email']]) ?>
                <table id="user-table" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Data de Criação</th>
                        <th>Data de Aprovação/Cancelamento</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    

                    <?php foreach ($saques as $saque) { ?>
                        <tr>
                            <td><?php echo $saque['created_at']?></td>
                            <td><?php echo ($saque['canceled_at'] ? $saque['canceled_at'] : $saque['approved_at'])?></td>
                            <td><?php echo $saque['valor'] ?></td>
                            <td><?php echo ($saque['status'] === 'AWAITING_FOR_APPROVAL' ? "Aguardando aprovação" : ($saque['status'] === 'CANCELED' ? "Cancelado" : 'Pago')) ?></td>
                        </tr>
                    <?php } ?>

                    <?php if (count($saques) == 0) {
                        echo "
                        <tr>
                            <td colspan='4'>
                                Nenhum saque foi realizado
                            </td>
                        </tr>";
                    } ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="intermission wf-section"></div>
    <div id="about" class="comic-book white wf-section">
        <div class="minting-container left w-container">
            <div class="w-layout-grid grid-2">
                <img src="arquivos/money.png" loading="lazy" width="240" alt="Roboto #6340" class="mint-card-image v2">
                <div>
                    <h2>Indique um amigo e ganhe R$ no PIX</h2>
                    <h3>Como funciona?</h3>
                    <p>Convide seus amigos que ainda não estão na plataforma. Você receberá
                        R$ <?php echo number_format($cpa, 2, '.', ''); ?> por cada amigo que
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
            <?php include '../components/footer.php'; ?>
            </div>
    </div>
    <script type="text/javascript">
    </script>
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
            @-webkit-keyframes ww-1d3e1845-0974-4ce9-92ae-64548dac571e-launcherOnOpen {
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

            @keyframes ww-1d3e1845-0974-4ce9-92ae-64548dac571e-launcherOnOpen {
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

            @keyframes ww-1d3e1845-0974-4ce9-92ae-64548dac571e-widgetOnLoad {
                0% {
                    opacity: 0;
                }

                100% {
                    opacity: 1;
                }
            }

            @-webkit-keyframes ww-1d3e1845-0974-4ce9-92ae-64548dac571e-widgetOnLoad {
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="../assets/extra-libs/sparkline/sparkline.js"></script>
<script src="../dist/js/waves.js"></script>
<script src="../dist/js/sidebarmenu.js"></script>
<script src="../dist/js/custom.min.js"></script>
<script src="../assets/extra-libs/multicheck/datatable-checkbox-init.js"></script>
<script src="../assets/extra-libs/multicheck/jquery.multicheck.js"></script>
<script src="../assets/extra-libs/DataTables/datatables.min.js"></script>
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
        // Escolhe uma notificação aleatória da lista
        var notification = notifications[Math.floor(Math.random() * notifications.length)];

        // Exibe a notificação com a cor configurada para verde (sucesso)
        Notiflix.Notify.Success(notification, option);

        // Define um intervalo fixo de 8 segundos para a próxima notificação
        setTimeout(show_notification, 8000);
    }

    // Inicia a primeira notificação após 8 segundos
    setTimeout(show_notification, 8000);
</script>
</body>
</html>