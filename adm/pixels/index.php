<?php
session_start();

require './../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./../../');
$dotenv->safeLoad();

if (!isset($_SESSION['emailadm'])) {
    $url = $_ENV['BASE_URL'];
    header("Location: $url/adm/login");
    exit();
}

include './../../connection.php';

$conn = connect();

$result = get_all('app');

/* $google_ads_tag = $result['google_ads_tag'];
$facebook_ads_tag = $result['facebook_ads_tag'];
$jivo_script = $result['jivo_script']; */

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

<div
        id="main-wrapper"
        data-layout="vertical"
        data-navbarbg="skin5"
        data-sidebartype="full"
        data-sidebar-position="absolute"
        data-header-position="absolute"
        data-boxed-layout="full"
>
    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5">
                <a class="navbar-brand" href="../">
                  
                    <span class="logo-text ms-2">
                <img
                        src="../assets/images/<?php echo $_ENV['LOGOTEXT_DESENVOLVEDORA'];?>"
                        width="150" height="50"
                        alt="homepage"
                        class="light-logo"
                />
              </span>

                    <a class="nav-toggler waves-effect waves-light d-block d-md-none"
                       href="javascript:void(0)"
                    ><i class="ti-menu ti-close"></i></a>
            </div>

            <div class="navbar-collapse collapse"
                 id="navbarSupportedContent"
                 data-navbarbg="skin5">
                <ul class="navbar-nav float-start me-auto">
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link sidebartoggler waves-effect waves-light"
                           href="javascript:void(0)"
                           data-sidebartype="mini-sidebar"
                        ><i class="mdi mdi-menu font-24"></i
                            ></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <?php include '../components/aside.php' ?>
    <style>
        .alignItems {
            display: flex !important;
            margin: 0 auto;
        }

        .divText {
            margin: 1vw auto;
            text-align: center;
        }

        .textTitulo {
            font-size: 2.5rem;
            font-weight: bold;
        }

        #user-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        #user-table th, #user-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        #user-table th {
            background-color: #f2f2f2;
        }

        #user-table input[type="text"] {
            width: 80px;
            padding: 5px;
            margin: 0;
            box-sizing: border-box;
            border: none;
            background-color: transparent;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
    <div class="page-wrapper">
        <div class="card">
            <div class="card-body">
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
                        padding: 20px;
                        margin: 20px 0;
                        min-width: 320px;
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

                    .divGeralGerenciar {
                        width: 20vw;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto;
                        flex-wrap: wrap;
                    }

                    .box-input {
                        background: #b6b7bf;
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
                <div class='divText'>
                    <h4 class="textTitulo card-title mb-0">Adicionar Script</h4>
                </div>
                <?php require '../components/error.php' ?>
                <?php require '../components/success.php' ?>
                <div class="box-container">
                    <div class="box">
                        <form action="inserir.php" method="post" id="editForm3">
                            <p class="title">Adicionar Pixel ao site:</p>
                            <p class="description">Nome do Script:</p>
                            <input class="box-input" name="nome" placeholder="Digite para inserir"/>
                            <p class="description2">Script:</p>
                            <textarea class="box-input"
                                          name="script"></textarea>
                            <p class="description2">Local onde será inserido:</p>
                            <options>
                                <select class="box-input" name="local" placeholder="Digite para inserir">
                                    <option value="header">Header</option>
                                    <option value="body">Body</option>
                                </select>
                            </options>
                            <p class="description2">Página em que será inserida:</p>
                            <options>
                                <select class="box-input" name="pagina" placeholder="Digite para inserir">
                                    <option value="todo-sem-jogo">Todos sem o Jogo</option>
                                    <option value="deposito">Deposito</option>
                                    <option value="obrigado">Obrigado</option>
                                    <option value="pix">Pagina de Pagamento</option>
                                    <option value="afiliado">Afiliado</option>
                                    <option value="cadastrar">Cadastro</option>
                                    <option value="login">Login</option>
                                    <option value="inicio">Inicio</option>
                                    <option value="painel">Painel</option>
                                    <option value="ranking">Ranking</option>
                                    <option value="saque">Saque Saldo</option>
                                    <option value="saque-afiliado">Saque Afiliado</option>
                                </select>
                            </options>
                            <button type="submit" class="alignItems btn box-btn btn-primary">Salvar Alterações</button>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class='divText'>
                        <h4 class="textTitulo card-title mb-0">Gerênciar Scripts</h4>
                    </div>
                    <?php foreach (get_all('pixels') as $pixel) { ?>
                        <div class="divGeralGerenciar box">
                            <form action="atualizar.php" method="post" id="editForm3">
                                <p class="title">Editar Script:</p>
                                <p class="description">Nome do Script:</p>
                                <input class="box-input" name="nome" value="<?php echo $pixel['nome'] ?>"/>
                                <p class="description2">Código do Script:</p>
                                <textarea class="box-input"
                                          name="script"><?= file_get_contents('../../uploads/pixels/' . $pixel['script']) ?></textarea>
                                <input type="hidden" name="id" value="<?php echo $pixel['id'] ?>"/>
                                <p class="description2">Local onde está inserido:</p>
                                <options>
                                    <select class="box-input" name="local">
                                        <option <?php echo($pixel['local'] == 'header' ? 'selected' : '') ?> value="header">Header</option>
                                        <option <?php echo($pixel['local'] == 'body' ? 'selected' : '') ?> value="body">Body</option>
                                    </select>
                                </options>
                                <p class="description2">Página em que será inserida:</p>
                                <options>
                                    <select class="box-input" name="pagina" placeholder="Digite para inserir">
                                        <option <?php echo($pixel['pagina'] == 'todo-sem-jogo' ? 'selected' : '') ?>    value="todo-sem-jogo">Todos sem o Jogo</option>
                                        <option <?php echo($pixel['pagina'] == 'deposito' ? 'selected' : '') ?>         value="deposito">Deposito</option>
                                        <option <?php echo($pixel['pagina'] == 'obrigado' ? 'selected' : '') ?>         value="obrigado">Obrigado</option>
                                        <option <?php echo($pixel['pagina'] == 'pix' ? 'selected' : '') ?>              value="pix">Pagina de Pagamento</option>
                                        <option <?php echo($pixel['pagina'] == 'afiliado' ? 'selected' : '') ?>         value="afiliado">Afiliado</option>
                                        <option <?php echo($pixel['pagina'] == 'cadastrar' ? 'selected' : '') ?>        value="cadastrar">Cadastro</option>
                                        <option <?php echo($pixel['pagina'] == 'login' ? 'selected' : '') ?>            value="login">Login</option>
                                        <option <?php echo($pixel['pagina'] == 'inicio' ? 'selected' : '') ?>           value="inicio">Inicio</option>
                                        <option <?php echo($pixel['pagina'] == 'painel' ? 'selected' : '') ?>           value="painel">Painel</option>
                                        <option <?php echo($pixel['pagina'] == 'ranking' ? 'selected' : '') ?>          value="ranking">Ranking</option>
                                        <option <?php echo($pixel['pagina'] == 'saque' ? 'selected' : '') ?>            value="saque">Saque Saldo</option>
                                        <option <?php echo($pixel['pagina'] == 'saque-afiliado' ? 'selected' : '') ?>   value="saque-afiliado">Saque Afiliado</option>
                                    </select>
                                </options>
                                <button type="submit" class="alignItems btn box-btn btn-primary">Salvar Alterações
                                </button>
                            </form>
                            <form action="deletar.php" method="post" id="editForm3">
                                <input type="hidden" name="id" value="<?php echo $pixel['id'] ?>"/>
                                <input type="hidden" name="delete" value="true"/>
                                <button type="submit" class="alignItems btn box-btn btn-danger">Deletar</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <?php require '../../components/footer.php'?>
</div>
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
</body>
</html>
