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
    <meta
        name="keywords"
        content="Admin Dashboard"
    />
    <meta
        name="description"
        content="Admin Dashboard"
    />
    <meta name="robots" content="noindex,nofollow"/>
    <title>Admin Dashboard</title>

    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="../assets/images/<?php echo $_ENV['LOGOICON_DESENVOLVEDORA'];?>"
    />
    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet"/>

</head>

<body>
<!-- ============================================================== -->
<!-- Preloader - style you can find in spinners.css -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- Main wrapper - style you can find in pages.scss -->
<!-- ============================================================== -->
<div
    id="main-wrapper"
    data-layout="vertical"
    data-navbarbg="skin5"
    data-sidebartype="full"
    data-sidebar-position="absolute"
    data-header-position="absolute"
    data-boxed-layout="full"
>
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <a class="navbar-brand" href="..">
                    <!-- Logo icon -->
                    
                    <!--End Logo icon -->
                    <!-- Logo text -->
                    <span class="logo-text ms-2">
                <!-- dark Logo text -->
                <img
                    src="../assets/images/<?php echo $_ENV['LOGOTEXT_DESENVOLVEDORA'];?>"
                    width="150" height="50"
                    alt="homepage"
                    class="light-logo"
                />
              </span>

                </a>

                <a
                    class="nav-toggler waves-effect waves-light d-block d-md-none"
                    href="javascript:void(0)"
                ><i class="ti-menu ti-close"></i
                    ></a>
            </div>

            <div
                class="navbar-collapse collapse"
                id="navbarSupportedContent"
                data-navbarbg="skin5">

                <ul class="navbar-nav float-start me-auto">
                    <li class="nav-item d-none d-lg-block">
                        <a
                            class="nav-link sidebartoggler waves-effect waves-light"
                            href="javascript:void(0)"
                            data-sidebartype="mini-sidebar"
                        ><i class="mdi mdi-menu font-24"></i
                            ></a>
                    </li>


                </ul>
            </div>
        </nav>
    </header>
    <!-- ==========    MENU    =================== -->
    <?php include '../components/aside.php' ?>


    <style>
        /* Estilos da tabela */
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
            width: 80px; /* Ajuste conforme necessário */
            padding: 5px;
            margin: 0;
            box-sizing: border-box;
            border: none; /* Remover as bordas dos inputs */
            background-color: transparent; /* Tornar os inputs transparentes */
        }

        /* Estilos do botão */
        .btn-success {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-wh {
            background: #6dbc6d;
            color: #000000;
            border: none;
            border-radius: 10px;
            height: 48px;
        }
    </style>
    <div class="page-wrapper p-4">
        <h2 class="card-title">Seja um Franqueado</h2>
        <div class="card">
            <div class="card-body">
                <p class="fs-4">
                    Agora você pode se tornar nosso sócio com nosso mais novo modelo de franquias, indique a
                    oportunidade de ser dono de um dos jogos e receba até R$20.000 por comissão, além de % do GGR das
                    plataformas.
                </p>
            </div>
        </div>
        <p class="fs-4">
            Abaixo explicaremos como você pode participar deste modelo de franquias.
        </p>
        <div class="card">
            <div class="card-body">
                <h3>Como funciona o sistema de franquia?</h3>
                <p class="fs-4">
                    O nosso programa de franquias paga uma % de comissão sobre o GGR dos clientes que você indicou, além
                    de um valor fixo por venda da franquia de até R$20.000 por venda.

                    Por exemplo, se a sua franquia indicada fez R$100.000 de lucro no mês e possui uma taxa GGR de 10%,
                    o valor gerado em GGR seria R$10.000. Portanto, a sua comissão seria R$5.000,00, já descontando os
                    5% da nossa taxa administrativa.
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Quem paga a comissão? </h3>
                <p class="fs-4">
                    Suas comissões de GGR serão repassadas de forma automática toda vez que um franqueado fizer uma
                    recarga da fatura GGR.
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Qual o valor da franquia?</h3>
                <p class="fs-4">
                    Cada venda que você fizer, deverá nos repassar R$2.000 + 5% GGR, qualquer valor que você cobrar
                    acima disso será o seu lucro, por exemplo: se você fizer uma venda a R$10.000 + 10% GGR, sua
                    comissão será de R$8.000 + 5% GGR pela indicação.
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="fs-4">
                    Você só irá se preocupar em vender! Iremos fazer todo o trabalho duro, faremos a instalação para seu
                    novo franqueado em até 24hrs, criaremos um grupo com nossa equipe de suporte, faremos a manutenção,
                    atualizações e hospedagem do franqueado de forma gratuita.
                </p>

                <div class="w-100 text-center">
                    <a href="https://api.whatsapp.com/send?phone=15997880475&text=Quero%20ser%20um%20franqueador%20da%20SistemasBet">
                        <button class="btn btn-wh fw-bold fs-5">QUERO SER UM FRANQUEADO</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../../components/footer.php'?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="../dist/js/waves.js"></script>
<script src="../dist/js/sidebarmenu.js"></script>
<script src="../dist/js/custom.min.js"></script>

</body>
</html>
