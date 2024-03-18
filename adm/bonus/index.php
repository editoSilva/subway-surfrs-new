<?php

session_start();

require './../../vendor/autoload.php';
include './../../connection.php';

$dotenv = Dotenv\Dotenv::createImmutable('./../../');
$dotenv->safeLoad();

if (!isset($_SESSION['emailadm'])) {
    $url = $_ENV['BASE_URL'];
    header("Location: $url/adm/login");
    exit();
}

$app = stmt("SELECT * FROM app");
$buttons = get_all('bonus');

foreach ($buttons as $button) {
    if ($button['deposito'] < $app['deposito_min']) {
        $_SESSION['warning'][] = "O valor de depósito do bônus de depósito deve ser maior que o valor de depósito mínimo. Altere o valor de depósito mínimo ou o valor de depósito do bônus de depósito.";
    }
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
            color: #b6b7bf;
            font-weight: 500;
        }

        .description2 {
            color: #b6b7bf;
            margin-top: 2vh;
            font-weight: 500;
        }

        .box-input {
            width: 100%;
            height: 32px;
            border-radius: 5px;
            border: 1px solid #dadbe5;
        }

        .box-btn {
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>

    <?php include '../components/aside.php' ?>

    <div class="page-wrapper">
        <div class="card">
            <div class="card-body">
                <?php include '../components/messages.php' ?>
                <div class="row align-items-center justify-content-center text-center">
                    <h3>Adicionar um novo bônus</h3>
                    <div class="box">
                        <form action="<?= $_ENV['BASE_URL'] ?>/adm/bonus/inserir.php" method="post">
                            <p class="title pt-4">Adicionar Bônus de Depósito</p>
                            <p class="description">Valor do depósto:</p>
                            <input class="box-input" name="deposito" placeholder="Digite para inserir"/>
                            <p class="description2">Bônus ganho:</p>
                            <input class="box-input" name="ganho" placeholder="Digite para inserir"/>
                            <button type="submit" class="btn box-btn btn-primary">
                                Adicionar Bônus
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row align-items-center justify-content-center text-center">
                    <h3>Editar bônus</h3>
                    <?php foreach ($buttons as $bonus) { ?>
                        <div class="box">
                            <div class="d-flex flex-column">
                                <form action="<?= $_ENV['BASE_URL'] ?>/adm/bonus/deletar.php" method="post"
                                      class="d-flex w-100 justify-content-end">
                                    <input type="hidden" name="id" value="<?php echo $bonus['id'] ?>"/>
                                    <button type="submit" class="btn btn-close bg-white"></button>
                                </form>
                                <form action="<?= $_ENV['BASE_URL'] ?>/adm/bonus/atualizar.php" method="post">
                                    <p class="title">Editar Bônus de Depósito</p>
                                    <p class="description">Valor do depósito:</p>
                                    <input class="box-input input-group" name="deposito"
                                           value="<?php echo $bonus['deposito'] ?>"/>
                                    <p class="description2">Bônus ganho:</p>
                                    <input class="box-input input-group" name="ganho"
                                           value="<?php echo $bonus['ganho'] ?>"/>
                                    <input type="hidden" name="id" value="<?php echo $bonus['id'] ?>"/>
                                    <button type="submit" class="  btn box-btn btn-primary">
                                        Salvar Alterações
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
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
    <script src="../dist/js/waves.js"></script>
    <script src="../dist/js/sidebarmenu.js"></script>
    <script src="../dist/js/custom.min.js"></script>

</body>

</html>