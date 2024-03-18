<?php
session_start();

if (!isset($_SESSION['emailadm'])) {
    $url = $_ENV['BASE_URL'];
    header("Location: $url/adm/login");
    exit();
}

require './../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . './../../');
$dotenv->safeLoad();

include './../../connection.php';

$app = get_all('app')[0];

if (stmt("SELECT min(deposito) as min from bonus")['min'] < $app['deposito_min']) {
    $_SESSION['warning'][] = 'O valor mínimo de depósito é maior que os valores de bônus cadastrados. Por favor, atualize os valores de bônus. Caso contrário, os bônus não serão aplicados corretamente. O menor valor de deposito é ' . stmt("SELECT min(deposito) as min from bonus")['min'] . '.';
}

if ($app['coin_value'] < 0.01) {
    $_SESSION['warning'][] = 'O valor da moeda é menor que 0.01. Esse valor é muito baixo e pode causar problemas no sistema. Por favor, atualize o valor da moeda. Caso contrário, os bônus não serão aplicados corretamente.';
}

if ($app['coin_value'] > 1) {
    $_SESSION['warning'][] = 'O valor da moeda é menor que 1, valores altos podem gerar ganhos muito altos.';
}

if ($app['rollover_saque'] < 2000) {
    $_SESSION['warning'][] = 'O valor do rollover é menor que 2000%, valores muito baixos podem gerar um grande número de saques.';
}

?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords" content="Admin Dashboard"/>
    <meta name="description" content="Admin Dashboard"/>
    <meta name="robots" content="noindex,nofollow"/>
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/<?php echo $_ENV['LOGOICON_DESENVOLVEDORA'];?>"/>
    <link href="../assets/libs/flot/css/float-chart.css" rel="stylesheet"/>
    <link href="../dist/css/style.min.css" rel="stylesheet"/>
</head>

<body>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5">
                <a class="navbar-brand" href="../">
            <span class="logo-text ms-2">
              <img src="../assets/images/<?php echo $_ENV['LOGOTEXT_DESENVOLVEDORA'];?>" width="150" height="50" alt="homepage" class="light-logo"/>
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
    <style>
        .box-container {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            width: 100%;
            justify-content: space-around;
        }

        .box {
            border: 1px solid white;
            padding: 1vw;
            margin: 2vw 1vw;
            min-width: 320px;
            width: 320px;
            background-color: #1f262d;
            border-radius: 15px;
            display: block;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: white;
        }

        .description {
            color: white;
            font-weight: 500;
        }

        .box-input {
            background: white;
            width: 100%;
            height: 32px;
            border-radius: 5px;
            border: 1px solid #dadbe5;
        }

        .box-btn {
            margin-top: 10px;
            border-radius: 5px;
        }

        .box-form {
            display: flex;
            flex-direction: column;
            align-items: baseline;
            justify-content: end;
        }
    </style>
    <?php include '../components/aside.php' ?>

    <div class="page-wrapper">
        <div class="card">
            <div class="card-body">
                <h3 class="pl-3 card-title mb-0">Configurações gerais</h3>
                <div class="container">
                    <?php include '../components/messages.php' ?>
                </div>
                <div class="row">
                    <div class="box-container">
                        <div class="box">
                            <p class="title">Dificuldade do Jogo:</p>
                            <p class="description">Quanto mais dificil, maior será a velocidade do jogo.<br>
                                (Recomendação: Dificil)<br>
                            </p>
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=dificuldade_jogo"
                                  class="box-form"
                                  method="post"
                                  >
                                <select name="valor" class="form-select custom-input"
                                        aria-label="Escolha a dificuldade">
                                    <option <?php echo ($app['dificuldade_jogo'] == 'facil') ? 'selected' : ''; ?>
                                            value="facil">Fácil
                                    </option>
                                    <option <?php echo ($app['dificuldade_jogo'] == 'medio') ? 'selected' : ''; ?>
                                            value="medio">Médio
                                    </option>
                                    <option <?php echo ($app['dificuldade_jogo'] == 'dificil') ? 'selected' : ''; ?>
                                            value="dificil">Difícil
                                    </option>
                                    <option <?php echo ($app['dificuldade_jogo'] == 'impossivel') ? 'selected' : ''; ?>
                                            value="impossivel">Impossível
                                    </option>
                                </select>
                                <button type="submit" class="btn box-btn btn-primary" onclick="atualizarValor4()">Salvar
                                    Alterações
                                </button>
                            </form>
                        </div>
                        <div class="box">
                            <p class="title">Depósito Mínimo (R$):</p>
                            <p class="description">Valor mínimo para depósitos.</p>
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=deposito_min"
                                  method="post"
                                  class="box-form"
                                  >
                                <input class="box-input" name="valor" value="<?php echo $app['deposito_min'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>
                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=saques_min"
                                  method="post"
                                   class="box-form">
                                <p class="title">Saque Mínimo (R$):</p>
                                <p class="description">Valor mínimo para saques.</p>
                                <input class="box-input" name="valor" value="<?php echo $app['saques_min'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>
                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=saques_max"
                                  method="post"
                                   class="box-form">
                                <p class="title">Saque Máximo (R$):</p>
                                <p class="description">Valor máximo para saques.</p>
                                <input class="box-input" name="valor" value="<?php echo $app['saques_max'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>

                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=aposta_max"
                                  method="post"
                                   class="box-form">
                                <p class="title">Aposta Máxima (R$):</p>
                                <p class="description">Valor máximo por aposta efetuada.</p>
                                <input class="box-input" name="valor" value="<?php echo $app['aposta_max'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>

                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=aposta_min"
                                  method="post"
                                   class="box-form">
                                <p class="title">Aposta Mínima (R$):</p>
                                <p class="description">Valor mínimo por aposta efetuada.</p>
                                <input class="box-input" name="valor" value="<?php echo $app['aposta_min'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>

                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=rollover_saque"
                                  method="post"
                                   class="box-form">
                                <p class="title">Rollover para Sacar (%):</p>
                                <p class="description">Porcentagem que o usuario deve movimentar do valor depositado
                                    para sacar.
                                    (Recomendação: 1000)<br>
                                </p>
                                <input class="box-input" name="valor" value="<?php echo $app['rollover_saque'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>

                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=taxa_saque"
                                  method="post"
                                   class="box-form">
                                <p class="title">Taxa em % no valor do saque:</p>
                                <p class="description">Porcentagem de acréscimo no valor do saque para cobranças de
                                    taxas no gateway.</p>
                                <input class="box-input" name="valor" value="<?php echo $app['taxa_saque'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>

                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=coin_value"
                                  method="post"
                                   class="box-form">
                                <p class="title">Valor por moeda (R$):</p>
                                <p class="description">
                                    Controla o valor que cada moeda vale.<br>
                                    Valor 0.99 p/ menos = Centavos<br>
                                    (Recomendação: 0.01)<br>
                                </p>

                                <input class="box-input" name="valor" value="<?php echo $app['coin_value'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>
                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=xmeta"
                                  method="post"
                                  class="box-form">
                                <p class="title">Meta usuário no jogo (*):</p>
                                <p class="description">Multiplicador na bet (bet*multiplicador) que o usuário precisa
                                    fazer para sacar.
                                    (Recomendação: 10)<br>
                                </p>
                                <input class="box-input" name="valor" value="<?php echo $app['xmeta'] ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Salvar Alterações</button>
                            </form>
                        </div>

                        <div class="box">
                            <form action="<?= $_ENV['BASE_URL'] ?>/adm/configuracoes/update.php?opcao=wppconnect-qrcode"
                                  method="post"
                                  class="box-form">
                                <p class="title">Gerar QrCode do Whatsapp:</p>
                                <p class="description">Clique aqui para gerar o qrcode do Whatsapp e então scaneie para realizar a integração do sistema.<br>
                                </p>
                                <input type="hidden" class="box-input" name="valor" value="<?php print_r($_SERVER['HTTP_HOST']) ?>"/>
                                <button type="submit" class="btn box-btn btn-primary">Gerar QrCode</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
            <?php require '../../components/footer.php'?>
        </div>
    </div>
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../assets/extra-libs/sparkline/sparkline.js"></script>
    <script src="../dist/js/sidebarmenu.js"></script>
    <script src="../dist/js/custom.min.js"></script>
</body>

</html>