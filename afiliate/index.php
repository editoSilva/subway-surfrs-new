<?php
session_start();


require './../vendor/autoload.php';
include './../connection.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . './../');
$dotenv->safeLoad();

$conn = connect();

// Verificar se o email está presente na sessão
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $getLinkQuery = "SELECT revenue_share_falso FROM app";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->execute();
    $stmt->bind_result($plano);
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

    $getLinkQuery = "SELECT cont_cpa FROM appconfig WHERE email = ?";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($cont_cpa);
    $stmt->fetch();
    $stmt->close();

    $getLinkQuery = "SELECT saldo_comissao FROM appconfig WHERE email = ?";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($saldo_comissao);
    $stmt->fetch();
    $stmt->close();

    // Consultar o valor da coluna linkafiliado para o email atual
    $getLinkQuery = "SELECT linkafiliado FROM appconfig WHERE email = ?";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($linkAfiliado);
    $stmt->fetch();
    $stmt->close();


    $getLinkQuery = "SELECT count(*) FROM appconfig WHERE lead_aff = (SELECT id FROM appconfig WHERE email = ? LIMIT 1)";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($cads);
    $stmt->fetch();
    $stmt->close();


    $getLinkQuery = "SELECT count(*) FROM appconfig WHERE lead_aff = (SELECT id FROM appconfig WHERE email = ? LIMIT 1) AND status_primeiro_deposito = 1";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($cad_ativo);
    $stmt->fetch();
    $stmt->close();

    //original
    //$getLinkQuery = "SELECT count(*) FROM appconfig WHERE afiliado = (SELECT id FROM appconfig WHERE email = ? LIMIT 1) AND status_primeiro_deposito = 1";
    //$getLinkQuery = "SELECT count(*) FROM appconfig WHERE afiliado = (SELECT id FROM appconfig WHERE email = ? LIMIT 1)";
    //$stmt = $conn->prepare($getLinkQuery);
    //$stmt->bind_param("s", $email);
    //$stmt->execute();
    //$stmt->bind_result($cad_ativo_sum);
    //$stmt->fetch();
    //$stmt->close();
    //$cad_ativo_sum = $cad_ativo_sum * $cpa;

    $getLinkQuery = "SELECT saldo_cpa, comissaofake, sacou, revenue_share, saldo_rev FROM appconfig WHERE email = ?";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($saldo_cpa, $comissaofake, $sacou, $rev_individual, $rev_ativo_sum);
    $stmt->fetch();
    $stmt->close();

    $rev_usado = 'revenue_share';

    if($rev_individual > 0){
        $rev_usado = $rev_individual;
    }

    /*$getLinkQuery = "SELECT IFNULL($rev_usado * (SELECT sum(percas) FROM appconfig WHERE afiliado = (SELECT id from appconfig WHERE email = ? LIMIT 1) AND status_primeiro_deposito = 1) / 100, 0) FROM app";
    $stmt = $conn->prepare($getLinkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($rev_ativo_sum);
    $stmt->fetch();
    $stmt->close();*/

    // Consultar o valor da coluna indicados para o email atual
    $getIndicadosQuery = "SELECT indicados FROM appconfig WHERE email = ?";
    $stmt = $conn->prepare($getIndicadosQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($indicados);
    $stmt->fetch();
    $stmt->close();
} else {
    // Redirecionar para a página de login se o email não estiver na sessão
    header("Location: /login");
    exit();
}


if ($comissaofake > 0) {
    $comissao_certa = $comissaofake;
} else {
    $comissao_certa = $plano;
}

$valor_saldo_cpa = isset($saldo_cpa) ? floatval($saldo_cpa) : 0;
$valor_rev_ativo_sum = isset($rev_ativo_sum) ? floatval($rev_ativo_sum) : 0;
$valor_sacou = isset($sacou) ? floatval($sacou) : 0;


?>


<!DOCTYPE html>

<html lang="pt-br" class="w-mod-js wf-spacemono-n4-active wf-spacemono-n7-active wf-active w-mod-ix">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        #hero{
            background-image:  url('../_next/background.png') !important;
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
    <?php require '../components/disable.php'; ?>
    <meta content="<?php echo $_ENV['NOME_SITE']; ?> 🌊" property="og:title">

    <meta name="twitter:image" content="../img/logo.png">

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
    <link rel="apple-touch-icon" sizes="180x180" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/logo.png">


    <link rel="icon" type="image/x-icon" href="../img/logo.png">

    <link rel="stylesheet" href="arquivos/css" media="all">

    <?php 
        if(stmt("SELECT count(*) as count from pixels WHERE local='header' AND (pagina='afiliado' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1){
            $pixel = stmt("SELECT * from pixels WHERE local='header' AND (pagina='afiliado' OR pagina='todas' OR pagina='todo-sem-jogo')");
            echo file_get_contents('../uploads/pixels/' . $pixel['script']);
        }else{
            foreach (stmt("SELECT * from pixels WHERE local='header' AND (pagina='afiliado' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) { 
            echo file_get_contents('../uploads/pixels/' . $pixel['script']);
            } 
        };
    ?>  

</head>

<body>
    <?php 
        if(stmt("SELECT count(*) as count from pixels WHERE local='body' AND (pagina='afiliado' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1){
            $pixel = stmt("SELECT * from pixels WHERE local='body' AND (pagina='afiliado' OR pagina='todas' OR pagina='todo-sem-jogo')");
            echo file_get_contents('../uploads/pixels/' . $pixel['script']);
        }else{
            foreach (stmt("SELECT * from pixels WHERE local='body' AND (pagina='afiliado' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) { 
            echo file_get_contents('../uploads/pixels/' . $pixel['script']);
            } 
        };
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
                <a href="../saque" class="nav-link w-nav-link" style="max-width: 940px;">Saque</a>

                <a href="../afiliate/" class="nav-link w-nav-link w--current" style="max-width: 940px;">Indique e
                    Ganhe</a>

                <a href="../logout.php" class="nav-link w-nav-link" style="max-width: 940px;">Sair</a>
                <a href="../deposito/" class="button nav w-button">DEPOSITAR</a>
            </nav>

            <style>
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


            <style>
                .menu-button2 {
                    border-radius: 15px;
                    background-color: #000;
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

        </a>
        <a href="../afiliate/" class="button w-button">
            <div>Indique & Ganhe</div>
        </a>
        <a href="../logout.php" class="button w-button">
            <div>Sair</div>
        </a>
        <a href="../deposito/" class="button w-button">DEPOSITAR</a>
    </div>

    <section id="hero" class="hero-section dark wf-section">
        <div class="minting-container w-container">
            <img src="arquivos/affiliate.gif" loading="lazy" width="240"
                 data-w-id="6449f730-ebd9-23f2-b6ad-c6fbce8937f7" alt="Roboto #6340" class="mint-card-image">

            <h2>Divulgue & Ganhe</h2>
            <p>Este é o resumo de seu resultado divulgando. <br>
            <p>Seu link de divulgação é: <br>
                 <b><?php echo $linkAfiliado; ?></b>
            </p>
            <br>

            <p>
                <a id="copiarLinkBtn" class="primary-button dark w-button" onclick="copiarLink()">Copiar link de
                    afiliado</a>
            </p>

            <br><br>

            <script>
                function copiarLink() {
                    var linkText = '<?php echo $linkAfiliado; ?>';
                    var input = document.createElement('textarea');
                    input.value = linkText;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    alert('Link copiado para a área de transferência: ' + linkText);
                }
            </script>

            <div class="properties">

                <div class="properties">
                    <h3 class="rarity-heading">Extrato</h3>
                    <div class="rarity-row roboto-type">
                        <div class="rarity-number full">CONTABILIZAÇÃO EM TEMPO REAL</div>
                    </div>
                    <div class="rarity-row roboto-type">
                        <div class="rarity-number full">Saldo disponível:</div>
                        <div class="padded">R$<?php echo number_format(($valor_saldo_cpa + ($valor_rev_ativo_sum >= 0 ? $valor_rev_ativo_sum : 0) - $valor_sacou), 2, '.', ''); ?></div>
                    </div>
                    <div class="w-layout-grid grid">
                        <div>
                            <div class="rarity-row blue">
                                <div class="rarity-number">Saldo CPA</div>
                                <div>R$ <?php echo number_format($saldo_cpa, 2, '.', ''); ?> </div>
                            </div>
                            <div class="rarity-row">
                                <div class="rarity-number">Revshare</div>
                                <div>R$ <?php echo number_format($rev_ativo_sum, 2, '.', ''); ?> </div>
                            </div>

                            <div class="rarity-row blue">
                                <div class="rarity-number">Cadastros totais</div>
                                <div><?php echo $cads; ?> cadastros</div>

                            </div>
                        </div>
                        <div>
                            <div class="rarity-row blue">
                                <div class="rarity-number">Cadastros ativos</div>
                                <div>
                                    <?php echo $cad_ativo ?> cadastros
                                </div>
                            </div>
                            <div class="rarity-row">
                                <div class="rarity-number">Valor por ativo</div>
                                <div>
                                    R$ <?php echo number_format($cpa, 2, '.', ''); ?>
                                </div>


                            </div>
                            <div class="rarity-row blue">
                                <div class="rarity-number">Revshare</div>
                                <div>
                                    <?php echo number_format($comissao_certa, 2, '.', ''); ?> %
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="grid-box">
                        <a href="../saque-afiliado" class="primary-button w-button">Sacar saldo disponível</a>
                        <a href="#" target="_blank" class="primary-button dark w-button">Suporte para afiliados</a>
                    </div>
                    <br>

                </div>
            </div>
    </section>


    <div class="intermission wf-section"></div>
    <div id="about" class="comic-book white wf-section">
        <div class="minting-container left w-container">
            <div class="w-layout-grid grid-2">
                <img src="arquivos/money.png" loading="lazy" width="240" alt="Roboto #6340" class="mint-card-image v2">
                <div>
                    <h2>COMO FUNCIONA O SISTEMA DE AFILIADOS?</h2>
                    <h3>DIVULGUE E FATURE</h3>
                    <p>O sistema de afiliados é construído para páginas, influenciadores, gestores de tráfego e profissionais do marketing digital. Você pode faturar muito mais divulgando a plataforma para o seu público.</p>
                    <h3>SAQUES PARA A CONTA BANCÁRIA</h3>
                    <p>Nossos saques ocorrem 24 horas por dia e 7 dias por semana. Basta solicitar via chave PIX no seu painel e em até 1 hora o dinheiro já estará na sua conta.</p>
                </div>
            </div>
        </div>
    </div>
   
 <div class="footer-section wf-section">
<div class="domo-text">SUBWAY <br>
</div>
<div class="domo-text purple">SURFERS <br>
</div>
<div class="follow-test">© Copyright Postbrands Limited, with registered offices at Dr. M.L. King Boulevard 117, accredited by license GLH-16286002012.<a /></a> </div>
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
        var hidden = false;

        $(document).ready(function () {
            $("form").submit(function () {
                $(this).submit(function () {
                    return false;
                });
                return true;
            });
        });

        function copyToClipboard(bt, text) {
            const elem = document.createElement('textarea');
            elem.value = text;
            document.body.appendChild(elem);
            elem.select();
            document.execCommand('copy');
            document.body.removeChild(elem);
            document.getElementById('depCopiaCodigo').innerHTML = "URL Copiada";
        }
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
            @-webkit-keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-launcherOnOpen {
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

            @keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-launcherOnOpen {
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

            @keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-widgetOnLoad {
                0% {
                    opacity: 0;
                }

                100% {
                    opacity: 1;
                }
            }

            @-webkit-keyframes ww-2989296f-947c-4706-b062-a6309b2b9b40-widgetOnLoad {
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
</body>

</html>