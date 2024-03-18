<?php


// Iniciar ou resumir a sessão
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../");
    exit();
}

require './../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./../');
$dotenv->safeLoad();


include './../connection.php';

$conn = connect();

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

$consulta_saldo = "SELECT saldo, bonus, saldo + saldo_fake as total, jogo_demo, saldo_fake FROM appconfig WHERE email = '$email'";
$resultado_saldo = $conn->query($consulta_saldo);

if ($resultado_saldo) {
    if ($resultado_saldo->num_rows > 0) {
        $row        = $resultado_saldo->fetch_assoc();
        $saldo      = $row['saldo'];
        $bonus      = $row['bonus'];
        $total      = $row['total'];
        $jogou_demo = $row['jogo_demo'];
    } else {
        $saldo = 0;
        $bonus = 0;
        $total = 0;
        $jogou_demo = 0;
    }
}

$consulta_app = "SELECT aposta_min, aposta_max, xmeta FROM app";
$get_app = $conn->query($consulta_app);

if ($get_app) {
    if ($get_app->num_rows > 0) {
        $row = $get_app->fetch_assoc();
        $aposta_min = $row['aposta_min'];
        $aposta_max = $row['aposta_max'];
        $xmeta      = $row['xmeta']; 
    }
}

if($jogou_demo <= 0){
    $disable_demo = 'disabled';
} else {
    $disable_demo = '';
}

?>


<!DOCTYPE html>

<html lang="pt-br" class="w-mod-js w-mod-ix wf-spacemono-n4-active wf-spacemono-n7-active wf-active">

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
    <meta content="<?php echo $_ENV['NOME_SITE']; ?> 🌊" property="og:title">
    <meta name="twitter:image" content="../img/logo.png">

    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="arquivos/page.css" rel="stylesheet" type="text/css">

    <?php require '../components/disable.php'; ?>
    <script type="text/javascript">
        WebFont.load({
            google: {
                families: ["Space Mono:regular,700"]
            }
        });
    </script>

    <link rel="apple-touch-icon" sizes="180x180" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/logo.png">


    <link rel="icon" type="image/x-icon" href="../img/logo.png">


    <link rel="stylesheet" href="arquivos/css" media="all">

    <?php 
  if(stmt("SELECT count(*) as count from pixels WHERE local='header' AND (pagina='painel' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1){
    $pixel = stmt("SELECT * from pixels WHERE local='header' AND (pagina='painel' OR pagina='todas' OR pagina='todo-sem-jogo')");
    echo file_get_contents('../uploads/pixels/' . $pixel['script']);
  }else{
    foreach (stmt("SELECT * from pixels WHERE local='header' AND (pagina='painel' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) { 
      echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    } 
  };
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
    if(stmt("SELECT count(*) as count from pixels WHERE local='body' AND (pagina='painel' OR pagina='todas' OR pagina='todo-sem-jogo')")['count'] == 1){
        $pixel = stmt("SELECT * from pixels WHERE local='body' AND (pagina='painel' OR pagina='todas' OR pagina='todo-sem-jogo')");
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
    }else{
        foreach (stmt("SELECT * from pixels WHERE local='body' AND (pagina='painel' OR pagina='todas' OR pagina='todo-sem-jogo')") as $pixel) { 
        echo file_get_contents('../uploads/pixels/' . $pixel['script']);
        } 
    };
    ?>
    <div class="divPrincipal">
        <div data-collapse="small" data-animation="default" data-duration="400" role="banner" class="navbar w-nav">
            <div class="container w-container">
                <a href="/painel" aria-current="page" class="brand w-nav-brand" aria-label="home">
                    <img src="arquivos/l2.png" loading="lazy" height="28" alt="" class="image-6">
                    <div class="nav-link logo"><?php echo $_ENV['NOME_SITE']; ?></div>
                </a>
                <nav role="navigation" class="nav-menu w-nav-menu">
                    <a href="../painel" class="nav-link w-nav-link" style="max-width: 940px;">Jogar</a>
                    <a href="../saque" class="nav-link w-nav-link" style="max-width: 940px;">Saque</a>

                    <a href="../afiliate/" class="nav-link w-nav-link" style="max-width: 940px;">Indique e Ganhe</a>

                    <a href="../logout.php" class="nav-link w-nav-link" style="max-width: 940px;">Sair</a>

                    <a href="../deposito/" class="button nav w-button">DEPOSITAR</a>
                </nav>
                <style>
                    .swal2-popup {
                        background-color: #ffffff;
                        border: 6px solid #000;
                        border-radius: 15px;
                    }

                    .swal2-confirm {
                        background-color: #fe1f4f !important;
                        border: 3px solid #000 !important;
                        font-size: 26px !important;
                        padding: 12px 24px !important;
                        width: 100px !important;
                        height: 60px !important;
                        text-transform: lowercase !important;
                        font-weight: bold !important;
                        box-shadow: -3px 3px 0 0 #1f2024 !important;
                    }

                    .swal2-confirm:hover {
                        background-color: #7066e0 !important;
                        border-radius: 5px !important;
                        border-color: #000 !important;
                        font-size: 26px !important;
                        font-weight: bold !important;
                        box-shadow: -6px 6px 0 0 #1f2024 !important;
                    }

                    .nav-bar {
                        display: none;
                        background-color: #333;
                        padding: 20px;
                        width: 90%;
                        position: fixed;
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

                    .menu-button2 {
                        border-radius: 15px;
                        background-color: #000;
                    }

                    .button.w-button {
                        text-align: center;
                    }

                    div.escudo {
                        display: block;
                        width: 247px;
                        line-height: 65px;
                        font-size: 12px;
                        margin: -60px 0 0 0;
                        background-image: url(/assets/img/game/escudo-branco.png);
                        background-size: contain;
                        background-repeat: no-repeat;
                        background-position: center;
                        filter: drop-shadow(1px 1px 3px #00000099) hue-rotate(0deg);
                    }

                    div.escudo img {
                        width: 50px;
                        margin: -10px 6px 0 0;
                    }

                    .divButtonBet {
                        display: flex;
                        justify-content: center;
                        align-items: start;
                        flex-direction: column;
                        width: 50vw;
                    }

                    .buttonValueBet {
                        width: 100%;
                        height: 5vh;
                        border: 0 solid #000;
                        text-align: start;
                        font-size: 1rem;
                        margin-left: 0.5vw;
                    }

                    .divButtonValueBet {
                        background-color: #E4F3FD;
                        border: dashed 0.5vh #a5cde2;
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        margin: 0 1.5vw 1.5vw 1.5vw;
                    }

                    .divInsideButtonValueBet {
                        display: flex;
                        background-color: #ffffff;
                        border: solid 0.3vh #9BBDD6;
                        padding: 0.5vw;
                        margin: 1vw;
                        justify-content: center;
                        align-items: center;
                        flex-direction: row;
                        width: 100%;
                    }

                    .textValueBet {
                        margin-left: 0;
                        font-size: 1rem;
                        font-weight: bold;
                        color: #000;
                        margin: 0 0 0 1.5vw
                    }

                    .buttonSubmitBet {
                        margin: 0 auto
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var menuButton = document.querySelector('.menu-button');
                        var navBar = document.querySelector('.nav-bar');

                        menuButton.addEventListener('click', function() {
                            // Toggle the visibility of the navigation bar
                            if (navBar.style.display === 'block') {
                                navBar.style.display = 'none';
                            } else {
                                navBar.style.display = 'block';
                            }
                        });
                    });
                </script>

                <div class="w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button" tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                    <div class="" style="-webkit-user-select: text;">


                        <a href="../deposito/" class="menu-button2 w-nav-dep nav w-button">DEPOSITAR</a>
                    </div>
                </div>
                <div class="menu-button w-nav-button" style="-webkit-user-select: text;" aria-label="menu" role="button" tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
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
        <div id="saldoDiv" style="position: absolute; top: 100px; width: 100%; line-height: 26px; color: #fff; z-index: 10; text-align: center;">
            SALDO: R$<b class="saldo"> <?php echo isset($total) ? number_format($total, 2, ',', '.') : '0,00'; ?> </b>
        </div>

        <section id="hero" class="hero-section dark wf-section">

            <div class="minting-container w-container">
                <div class="escudo">
                    <a href="/ranking">Ranking</a>
                    <img src="arquivos/trophy.gif">
                    <a href="/ranking">Ranking</a>
                </div>
                <h2>COMEÇAR CORRIDA</h2>
                <p>Colete as moedas e os propulsores para multiplicar ainda mais os seus ganhos, mas cuidado para não bater nos obstáculos</p>
                <p>⚠️ Não bata nos obstáculos</p>
                <p>❌ Não deixe o guarda te pegar</p>
                <p>✅ Colete as moedas e propulsores</p>

                <form id="formSubtrair1" action="processar_subtracao.php" method="post" aria-label="Form" onsubmit="return submeterFormulario('1.00')">
                    <div class="divButtonBet">
                        <p class='textValueBet'>Valor de Entrada:</p>
                        <div class="divButtonValueBet">
                            <div class="divInsideButtonValueBet">
                                <label class='cifraoLabel'>R$</label>
                                <input type="number" min="<?php echo floatval($aposta_min) ?>" max="<?php echo floatval($aposta_max) ?>" class="buttonValueBet" name="valor">
                            </div>
                        </div>
                        <input type="submit" min="<?php echo floatval($aposta_min) ?>" max="<?php echo floatval($aposta_max) ?>" value='JOGAR' class="buttonSubmitBet primary-button w-button" required>
                        <?php require '../adm/components/messages.php'; ?>
                        <br><br>
                    </div>
                </form>
                <script>
                    function submeterFormulario(valor) {
                        const total = <?php echo $total ?>;
                        if ((total) > 0) {
                            return true;
                        } else {
                            alert('Saldo é insuficiente. Faça um Depósito.');
                            return false;
                        }
                    }
                </script>
                <?php if($disable_demo != 'disabled'){?>
                <p>Você tem
                    <?php echo $jogou_demo; ?> tentativas!
                </p>
                <form data-name="" id="auth" method="post" aria-label="Form" action="../jogodemo">
                    <div class="">
                        <input type="submit" <?php echo $disable_demo; ?> value="Testar" class="primary-button w-button"><br><br>
                    </div>
                </form>
                <?php } ?>
                <i style="font-size: 10px;">Sua meta(ganho) é <?php echo $xmeta ?>x o valor apostado!</i>
            </div>
            <div id="wins" style="
                display: block;
                width: 240px;
                font-size: 12px;
                padding: 5px 0;
                text-align: center;
                line-height: 13px;
                background-color: #FFC107;
                border-radius: 10px;
                border: 3px solid #1f2024;
                box-shadow: -3px 3px 0 0px #1f2024;
                margin: -24px auto 0 auto;
                z-index: 1000;
            ">Usuários Online<br class="jWQDfMST8B">7283</div>
        </section>
        <section id="mint" class="mint-section wf-section">
            <div class="minting-container w-container">
                <img src="arquivos/asset.gif" loading="lazy" width="240" alt="" class="mint-card-image">
                <h2><?php echo $_ENV['NOME_SITE']; ?></h2>
                <p class="paragraph">
                    Prepare-se para uma aventura eletrizante nos trilhos, onde cada curva guarda a promessa de fortuna.
                    Desvie dos obstáculos, colete moedas reluzentes e desbloqueie novos percursos enquanto corre em
                    busca da riqueza. Sua jornada pela cidade começa agora – acelere, desfrute e acumule sua fortuna nos
                    trilhos de <?php echo $_ENV['NOME_SITE']; ?>!. </p>


                <a href="/deposito" class="primary-button w-button w--current">DEPOSITE</a>

            </div>
        </section>
        <div class="intermission wf-section">
            <div data-w-id="aa174648-9ada-54b0-13ed-6d6e7fd17602" class="center-image-block">
                <img src="arquivos/" loading="eager" alt="">
            </div>
            <div data-w-id="6d7abe68-30ca-d561-87e1-a0ecfd613036" class="center-image-block _2">
                <img src="arquivos/" loading="eager" alt="">
            </div>
            <div data-w-id="e04b4de1-df2a-410e-ce98-53cd027861f6" class="center-image-block _2">
                <img src="arquivos/" loading="eager" alt="" class="image-3">
            </div>
        </div>
    </div>
    <div id="faq" class="faq-section">
        <div class="faq-container w-container">
            <h2>faq</h2>
            <div class="question first">
                <img src="arquivos/" loading="lazy" width="110" alt="">
                <h3>Como funciona?</h3>
                <div><?php echo $_ENV['NOME_SITE']; ?> é o mais novo jogo divertido e lucrativo da galera! Lembra daquele joguinho de surfar
                    por cima dos trens que todo mundo era viciado? Ele voltou e agora dá para ganhar dinheiro de
                    verdade, mas cuidado com os obstáculos para você garantir o seu prêmio. É super simples, surf,
                    desvie dos obstáculos e colete seus prêmios.
                </div>
            </div>
            <div class="question">
                <img src="arquivos/60fa0061a0450e3b6f52e12f_Body.svg" loading="lazy" width="90" alt="">
                <h3>Como posso jogar?</h3>
                <div class="w-richtext">
                    <p>Você precisa fazer um depósito inicial na plataforma para começar a jogar e faturar.
                        Lembrando
                        que você indicando amigos, você ganhará dinheiro de verdade na sua conta bancária.</p>
                </div>
            </div>
            <div class="question">
                <img src="arquivos/61070a430f976c13396eee00_Gradient Shades.svg" loading="lazy" width="120" alt="">
                <h3>Como posso sacar? <br>
                </h3>
                <p>O saque é instantâneo. Utilizamos a sua chave PIX como CPF para enviar o pagamento, é na hora e
                    no
                    PIX. 7 dias por semana e 24 horas por dia. <br>
                </p>
            </div>
            <div class="question">
                <img src="arquivos/60fa004b7690e70dded91f9a_light.svg" loading="lazy" width="80" alt="">
                <h3>É tipo foguetinho?</h3>
                <div>
                    <b>Não</b>! <?php echo $_ENV['NOME_SITE']; ?> é totalmente diferente, basta apenas estar atento para desviar dos
                    obstáculos na hora certa. Não existe sua sorte em jogo, basta ter foco e completar o percurso
                    até resgatar o máximo de moedas que conseguir.
                </div>
            </div>
            <div class="question">
                <img src="arquivos/60f8d0c69b41fe00d53e8807_Helmet.svg" loading="lazy" width="90" alt="">
                <h3>Existem eventos?</h3>
                <div class="w-richtext">
                    <ul role="list">
                        <li>
                            <strong>Jogatina</strong>. Quanto mais você correr, mais moedas você coleta e mais
                            dinheiro você ganha. Mas cuidado! Há trens escondidas entre as
                            ruas.
                        </li>
                        <li>
                            <strong>Torneios</strong>. Além disso, você pode competir com outros jogadores em
                            torneios e
                            desafios diários para ver quem consegue a maior pontuação e fatura mais dinheiro. A
                            emoção
                            da competição e a chance de ganhar grandes prêmios adicionam uma camada extra de
                            adrenalina
                            ao jogo.
                        </li>
                    </ul>
                    <p>Clique <a href="#">aqui</a> e acesse nosso grupo no Telegram
                        para
                        participar de eventos exclusivos. </p>
                </div>
            </div>
            <div class="question last">
                <img src="arquivos/60f8d0c657c9a88fe4b40335_Exploded Head.svg" loading="lazy" width="72" alt="">
                <h3>Dá para ganhar mais?</h3>
                <div class="w-richtext">
                    <p>Chame um amigo para jogar e após o depósito e a primeira partida será creditado em sua conta
                        R$15
                        para sacar a qualquer momento. </p>
                    <ol role="list">
                        <li>O saldo é adicionado diretamente ao seu saldo em dinheiro, com o qual você pode jogar ou
                            sacar. </li>
                        <li>Seu amigo deve se inscrever através do seu link de convite pessoal. </li>
                        <li>Seu amigo deve ter depositado pelo menos R$25.00 BRL para receber o prêmio do convite.
                        </li>
                        <li>Você não pode criar novas contas na <?php echo $_ENV['NOME_SITE']; ?> e se inscrever através do seu próprio link
                            para receber a recompensa. O programa Indique um Amigo é feito para nossos jogadores
                            convidarem amigos para a plataforma <?php echo $_ENV['NOME_SITE']; ?>. Qualquer outro uso deste programa é
                            estritamente proibido. </li>
                    </ol>
                    <p>‍</p>
                </div>
            </div>
        </div>
        <div class="faq-left">
            <img src="arquivos/60f988c7c856f076b39f8fa4_head 04.svg" loading="eager" width="238.5" alt="" class="faq-img" style="opacity: 0;">
            <img src=".arquivos/60f988c9402afc1dd3f629fe_head 26.svg" loading="eager" width="234" alt="" class="faq-img _1" style="opacity: 0;">
            <img src="arquivos/60f988c9bc584ead82ad8416_head 29.svg" loading="lazy" width="234" alt="" class="faq-img _2" style="opacity: 0;">
            <img src="arquivos/60f988c913f0ba744c9aa13e_head 27.svg" loading="lazy" width="234" alt="" class="faq-img _3" style="opacity: 0;">
            <img src="arquivos/60f988c9d3d37e14794eca22_head 25.svg" loading="lazy" width="234" alt="" class="faq-img _1" style="opacity: 0;">
            <img src="arquivos/60f988c98b7854f0327f5394_head 24.svg" loading="lazy" width="234" alt="" class="faq-img _2" style="opacity: 0;">
            <img src="arquivos/60f988c82f5c199c4d2f6b9f_head 05.svg" loading="lazy" width="234" alt="" class="faq-img _3" style="opacity: 0;">
        </div>
        <div class="faq-right">
            <img src="arquivos/60f988c88b7854b5127f5393_head 23.svg" loading="eager" width="238.5" alt="" class="faq-img" style="opacity: 0;">
            <img src="arquivos/60f988c8bf76d754b9c48573_head 12.svg" loading="eager" width="234" alt="" class="faq-img _1" style="opacity: 0;">
            <img src="arquivos/60f988c8f2b58f55b60d858f_head 21.svg" loading="lazy" width="234" alt="" class="faq-img _2" style="opacity: 0;">
            <img src="arquivos/60f988c8e83a994a38909bc4_head 22.svg" loading="lazy" width="234" alt="" class="faq-img _3" style="opacity: 0;">
            <img src="arquivos/60f988c8a97a7c125d72046d_head 20.svg" loading="lazy" width="234" alt="" class="faq-img _1" style="opacity: 0;">
            <img src="arquivos/60f988c8fbbbfe5fc68169e0_head 14.svg" loading="lazy" width="234" alt="" class="faq-img _2" style="opacity: 0;">
            <img src="arquivos/60f988c88b7854b35e7f5390_head 18.svg" loading="lazy" width="234" alt="" class="faq-img _3" style="opacity: 0;">
        </div>
        <div class="faq-bottom">
            <img src="arquivos/60f988c8ba5339712b3317c0_head 16.svg" loading="lazy" width="234" alt="" class="faq-img _3" style="opacity: 0;">
            <img src="arquivos/60f988c86e8603bce1c16a98_head 17.svg" loading="lazy" width="234" alt="" class="faq-img" style="opacity: 0;">
            <img src="arquivos/60f988c889b7b12755035f2f_head 19.svg" loading="lazy" width="234" alt="" class="faq-img _1" style="opacity: 0;">
        </div>
        <div class="faq-top">
            <img src="arquivos/60f988c8a97a7ccf6f72046a_head 11.svg" loading="eager" width="234" alt="" class="faq-img _3" style="opacity: 0;">
            <img src="arquivos/60f988c7fbbbfed6f88169df_head 02.svg" loading="eager" width="234" alt="" class="faq-img" style="opacity: 0;">
            <img src="arquivos/60f8dbc385822360571c62e0_icon-256w.png" loading="eager" width="234" alt="" class="faq-img _1" style="opacity: 0;">
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




    <div id="imageDownloaderSidebarContainer">
        <div class="image-downloader-ext-container">
            <div tabindex="-1" class="b-sidebar-outer"><!---->
                <div id="image-downloader-sidebar" tabindex="-1" role="dialog" aria-modal="false" aria-hidden="true" class="b-sidebar shadow b-sidebar-right bg-light text-dark" style="width: 500px; display: none;"><!---->
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
                @-webkit-keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-launcherOnOpen {
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

                @keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-launcherOnOpen {
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

                @keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-widgetOnLoad {
                    0% {
                        opacity: 0;
                    }

                    100% {
                        opacity: 1;
                    }
                }

                @-webkit-keyframes ww-c5d711d7-9084-48ed-a561-d5b5f32aa3a5-widgetOnLoad {
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
                    title: 'VOCÊ VENCEU!',
                    html: 'Parabéns! você ganhou nesse jogo:<br/> <strong style="color: #11d97f;">R$ ' + acumulado + '</strong> continue assim campeão!!',
                    icon: 'success',
                });
            } else if (msg == 'gameover') {
                Swal.fire({
                    title: 'QUE PENA!',
                    html: 'Você perdeu, mas não desanime, tente novamente!',
                    icon: 'error',
                });
            } else if(msg == 'bet_failed'){
                Swal.fire({
                    title: 'QUE PENA!',
                    html: 'Você não tem saldo suficiente, recarregue e tente novamente!',
                    icon: 'error',
                });
            }else if(msg == 'gameNotFound'){
                Swal.fire({
                    title: 'ERRO!',
                    html: 'Sessão de Jogo Expirada ou Não Encontrada!',
                    icon: 'error',
                });
            } else if(msg == 'fatalError'){
                Swal.fire({
                    title: 'ERRO!',
                    html: 'Obtivemos um erro inesperado! Contate o suporte imediatamente',
                    icon: 'error',
                });
            }
        });
    </script>

    <script>
        var position = "left-bottom";
        var animation = "from-left"; 
        var timeout = 4000; 
    
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
    
        setTimeout(show_notification, 3000);
    </script>
</body>


</html>