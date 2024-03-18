<?php
    session_start();

    require './../../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . './../../');
    $dotenv->safeLoad();

    if (!isset($_SESSION['emailadm'])) {
        $url = $_ENV['BASE_URL'];
        header("Location: $url/adm/login");
        exit();
    }

    include './../../connection.php';
    $conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
    
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }
    
    $result = $conn->query("SELECT * FROM app LIMIT 1");
    $result = $result->fetch_assoc();
    
    $cpa = $result['cpa'];
    $chance_afiliado = $result['chance_afiliado'];
    $deposito_min_cpa = $result['deposito_min_cpa'];
    $revenue_share_falso = $result['revenue_share_falso'];
    $max_saque_cpa = $result['max_saque_cpa'];
    $max_por_saque_cpa = $result['max_por_saque_cpa'];
    $revenue_share = $result['revenue_share'];
    
    
    
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
<style>
    .divPremiacao{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center; 
    }
    
    .placa_premiacao{
        margin: 1vw 0 3vw 0 !important;
    }
</style>

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
                <a class="navbar-brand" href="../">
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
        .navbarSupportedContent{
            height: 150 !important;    
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
    </style>
    <div class="page-wrapper">
        <div class="card">
            <div class="divPremiacao card-body">
                <img
                  src="../assets/images/placa_premiacao2.png"
                  alt="premiacao"
                  class="placa_premiacao light-logo"
                  width="600"
                />
                <p class="fs-4">
                    Na Sistemas Bet, acreditamos no poder do reconhecimento e da motivação para impulsionar o seu
                    sucesso! Por isso, apresentamos o nosso Programa de Premiações com as cobiçadas Placas Bet,
                    especialmente desenhadas para inspirar e celebrar as suas conquistas excepcionais. 😍
                </p>
                <p class="fs-4">
                    Queremos que você se sinta valorizado(a) em cada etapa do seu negócio, e é por isso que criamos uma
                    jornada emocionante para você trilhar. Cada placa representa um marco significativo em seu
                    crescimento e progresso na Sistemas Bet.
                </p>
                <p class="fs-4">
                    Veja como funciona:
                </p>
                <h3>Placa Prata</h3>
                <p class="fs-4">
                    A partir do momento em que você alcança R$10.000 em faturamento, você conquista a Placa Prata. Essa
                    placa celebra as suas habilidades excepcionais e comprova o seu talento em gerar resultados
                    expressivos.
                </p>
                <h3>Placa Ouro</h3>
                <p class="fs-4">
                    A sua criatividade e capacidade de adaptação ao mercado dinâmico o levarão a R$100.000 em
                    faturamento, onde você será honrado(a) com a Placa Ouro. Essa placa reconhece a sua visão inovadora
                    e determinação em enfrentar desafios com maestria.
                </p>
                <h3>Placa Diamente</h3>
                <p class="fs-4">
                    Ao chegar aos incríveis R$1.000.000 em faturamento, você será merecidamente homenageado(a) com a
                    Placa Diamante. Essa placa celebra o seu sucesso sem precedentes, esta placa reconhece você como
                    um(a) verdadeiro(a) inovador(a) e pioneiro(a) no i-gaming.

                    Essas placas são mais do que símbolos. Elas são testemunhos do seu potencial ilimitado e da sua
                    capacidade de alcançar resultados extraordinários.
                </p>
            </div>
            <div class="w-100 text-center">
                <h2>Junte-se aos vencedores. A trajetória de sucesso começa agora!</h2>
            </div>
        </div>
    </div>
    <?php require '../../components/footer.php'?>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <!-- ============================================================== -->
    <!-- End footer -->
    <!-- ============================================================== -->
</div>
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<!--Wave Effects -->
<script src="../dist/js/waves.js"></script>
<!--Menu sidebar -->
<script src="../dist/js/sidebarmenu.js"></script>
<!--Custom JavaScript -->
<script src="../dist/js/custom.min.js"></script>

</body>
</html>
