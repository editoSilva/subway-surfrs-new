<?php
session_start();

if (!isset($_SESSION['emailadm'])) {
    header("Location: login");
    exit();
}

require './../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./../');
$dotenv->safeLoad();

include './../connection.php';

$conn = connect();

$sql = "SELECT * FROM app";
$result2 = $conn->query($sql);
$result = $result2->fetch_assoc();

$pix_gerados_24h = $conn->query("SELECT COUNT(*) as count FROM confirmar_deposito WHERE data >= DATE_SUB(NOW(), INTERVAL 1 DAY)")->fetch_assoc()['count'];
$pix_pagos_24h = $conn->query("SELECT COUNT(*) as count FROM confirmar_deposito WHERE data >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'PAID_OUT'")->fetch_assoc()['count'];
$pix_gerados = $conn->query("SELECT COUNT(*) as count FROM confirmar_deposito")->fetch_assoc()['count'];
$pix_pagos = $conn->query("SELECT COUNT(*) as count FROM confirmar_deposito WHERE status = 'PAID_OUT'")->fetch_assoc()['count'];
$cadastros_24h = $conn->query("SELECT COUNT(*) as count FROM appconfig WHERE data_cadastro >= (NOW() - INTERVAL 1 DAY)")->fetch_assoc()['count'];
$cadastros_7d = $conn->query("SELECT COUNT(*) as count FROM appconfig WHERE data_cadastro >= (NOW() - INTERVAL 7 DAY)")->fetch_assoc()['count'];
$cadastros_30d = $conn->query("SELECT COUNT(*) as count FROM appconfig WHERE data_cadastro >= (NOW() - INTERVAL 30 DAY)")->fetch_assoc()['count'];
$cadastros = $conn->query("SELECT COUNT(*) as count FROM appconfig")->fetch_assoc()['count'];

$sql = "SELECT * FROM ggr";
$result_ggr = $conn->query($sql);
$result_ggr2 = $result_ggr->fetch_assoc();
$total_saques = $conn->query("SELECT SUM(sum) as total FROM ( SELECT COALESCE(SUM(valor), 0) as sum FROM saques WHERE status='PAID' UNION ALL SELECT COALESCE(SUM(valor), 0) as sum FROM saque_afiliado WHERE status='PAID' ) as subquery")->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="keywords" content="Admin Dashboard" />
    <meta name="description" content="Admin Dashboard" />
    <meta name="robots" content="noindex,nofollow" />
    <title>Admin Dashboard</title>

    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/<?php echo $_ENV['FAVICON_DESENVOLVEDORA']; ?>" />
    <!-- Custom CSS -->
    <link href="assets/libs/flot/css/float-chart.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="dist/css/style.min.css" rel="stylesheet" />

</head>

<style>
    #chart_div #chart_div2 {
        width: 80vw;
        margin: 50px auto;
    }

    .divGraficosNovosUsuarios {
        margin: 2vw 0;
    }

    .buttonGGR {
        background-color: black;
        color: white;
        border: none;
        padding: 0.35vw;
        border-radius: 12px;
        transition: all 0.5s;
    }

    .buttonGGR:hover {
        background-color: white;
        color: black;
    }

    .tituloChart {
        font-size: 1.5rem;
    }

    .bold-red {
        font-size: 1rem;
        color: red;
    }

    .popup {
        display: none;
        position: fixed;
        text-align: justify;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4)
    }

    .divPopup {
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 30vw;
    }

    .divEstatisticas {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }
</style>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <a class="navbar-brand" href="#">
                        <span class="logo-text ms-2">
                            <img src="assets/images/<?php echo $_ENV['LOGOTEXT_DESENVOLVEDORA']; ?>" width="150" height="50" alt="homepage" class="light-logo" />
                        </span>
                    </a>
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                    <ul class="navbar-nav float-start me-auto">
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <?php include 'components/aside.php' ?>
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Dashboard</h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">

                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">

                    <div class="divEstatisticas">
                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">PIX'S GERADOS 24H</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $pix_gerados_24h ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">PIX'S PAGOS 24H</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $pix_pagos_24h ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">PIX'S GERADOS</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $pix_gerados ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">PIX'S PAGOS</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $pix_pagos ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">CADASTROS 24H</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $cadastros_24h ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">CADASTROS 7D</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $cadastros_7d ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">CADASTROS 30D</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $cadastros_30d ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 col-xlg-3 mb-3">
                            <div class="card card-hover">
                                <div class="box bg-dark text-center">
                                    <h1 class="font-light text-white">
                                        <i class="mdi mdi-arrow-down-bold"></i>
                                    </h1>
                                    <h5 class="text-white">CADASTROS TOTAIS</h5>
                                    <label class="text-white" id="valorApostaMax" name="valor"><?php echo $cadastros ?></label>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!-- ============================================================== -->

                <script>
                    // Evento de clique ou outra ação que aciona a leitura
                    $(document).ready(function() {
                        // Solicitação AJAX
                        $.ajax({
                            type: "GET",
                            url: "php/app.php",
                            success: function(response) {
                                // Atualiza o valor exibido na página
                                $("#valorUsuarios").text(response);
                                console.log(response); // Exibe a resposta do servidor no console
                            },
                            error: function(error) {
                                console.log("Erro na solicitação AJAX: " + error);
                            }
                        });
                    });
                </script>


                <div class="row">


                    <div class="col-md-6 col-lg-4 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white" id="valorUsuarios3">
                                    R$ 0,00
                                </h1>
                                <h4 class="text-white">Total depositado</h4>
                            </div>
                        </div>
                    </div>


                    <!-- ============================================================== -->
                    <script>
                        // Evento de clique ou outra ação que aciona a leitura
                        $(document).ready(function() {
                            // Solicitação AJAX
                            $.ajax({
                                type: "GET",
                                url: "php/total_depositos.php",
                                success: function(response) {
                                    // Atualiza o valor exibido na página
                                    $("#valorUsuarios3").text(response);
                                    console.log(response); // Exibe a resposta do servidor no console
                                },
                                error: function(error) {
                                    console.log("Erro na solicitação AJAX: " + error);
                                }
                            });
                        });
                    </script>
                    <div class="col-md-6 col-lg-4 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-danger text-center">

                                <h1 class="font-light text-white" id="valorUsuarios2">
                                    0
                                </h1>
                                <h4 class="text-white">Total de cadastros</h4>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            $.ajax({
                                type: "GET",
                                url: "php/app.php",
                                success: function(response) {
                                    $("#valorUsuarios2").text(response);
                                    console.log(response);
                                },
                                error: function(error) {
                                    console.log("Erro na solicitação AJAX: " + error);
                                }
                            });
                        });
                    </script>
                    <div class="col-md-6 col-lg-4 col-xlg-3">
                        <div class="card card-hover">
                            <div class="box bg-danger text-center">
                                <h1 class="font-light text-white">
                                    R$ <?php echo $total_saques; ?>
                                </h1>
                                <h4 class="text-white">Total de Saques</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="divChart">
                                <p class="tituloChart"><b>Depósitos e Retiradas</b></p>
                                <div class="graficoDados" id="chart_div2"></div>

                            </div>
                            <div class="divGraficosNovosUsuarios">
                                <p class="tituloChart"><b>Novos Usuários</b></p>
                                <div class="graficoDados" id="chart_div"></div>
                                <div id="chart_div"></div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .custom-input {
                            width: 100px;
                            background-color: transparent;
                            color: white;
                            display: inline-block;
                            font-weight: 700;
                            font-size: 18px;
                        }
                    </style>

                    <script>
                        google.charts.load('current', {
                            packages: ['corechart']
                        });
                        google.charts.setOnLoadCallback(drawBasic);

                        function drawBasic() {
                            $.ajax({
                                url: 'dadosgrafico2.php',
                                dataType: 'json',
                                success: function(data) {
                                    // Transforma o formato dos dados
                                    var transformedData = data.map(function(item) {
                                        return [item.dia, item.total_deposito, '<div style="font-family: Arial; font-size: 1rem; width: 8vw; text-align: center;">Dia: ' + item.dia + '<br>Depósitos: <b>R$' + item.total_deposito + '</b></div>', item.total_saques, '<div style="font-family: Arial; font-size: 1rem; width: 8vw; text-align: center;">Dia: ' + item.dia + '<br>Saques: <b>R$' + item.total_saques + '</b></div>'];
                                    });

                                    var dataTable = new google.visualization.DataTable();
                                    dataTable.addColumn('number', 'Dia');
                                    dataTable.addColumn('number', 'Depósitos');
                                    dataTable.addColumn({
                                        type: 'string',
                                        role: 'tooltip',
                                        'p': {
                                            'html': true
                                        }
                                    });
                                    dataTable.addColumn('number', 'Saques');
                                    dataTable.addColumn({
                                        type: 'string',
                                        role: 'tooltip',
                                        'p': {
                                            'html': true
                                        }
                                    });
                                    dataTable.addRows(transformedData);

                                    var options = {
                                        hAxis: {
                                            title: 'Dia',
                                            format: '0'
                                        },
                                        vAxis: {
                                            title: 'Valores'
                                        },
                                        series: {
                                            0: {
                                                targetAxisIndex: 0
                                            },
                                            1: {
                                                targetAxisIndex: 1
                                            }
                                        },
                                        axes: {
                                            y: {
                                                0: {
                                                    label: 'Depósitos'
                                                },
                                                1: {
                                                    label: 'Saques'
                                                }
                                            }
                                        },
                                        tooltip: {
                                            isHtml: true
                                        }
                                    };

                                    var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));
                                    chart.draw(dataTable, options);
                                }
                            });
                        }
                    </script>

                    <script>
                        google.charts.load('current', {
                            packages: ['corechart', 'line']
                        });
                        google.charts.setOnLoadCallback(drawBasic);

                        function drawBasic() {
                            $.ajax({
                                url: 'dadosgrafico.php',
                                dataType: 'json',
                                success: function(data) {
                                    // Transforma o formato dos dados
                                    var transformedData = data.map(function(item) {
                                        return [item.dia, item.total_cadastros, '<div style="font-family: Arial; font-size: 1rem; width: 12vw; text-align: center;">Dia: ' + item.dia + '<br/>Cadastros por Dia: <b>' + item.total_cadastros + '</b></div>'];
                                    });

                                    // Cria a DataTable
                                    var dataTable = new google.visualization.DataTable();
                                    dataTable.addColumn('number', 'Dia');
                                    dataTable.addColumn('number', 'Cadastros por Dia');
                                    dataTable.addColumn({
                                        type: 'string',
                                        role: 'tooltip',
                                        'p': {
                                            'html': true
                                        }
                                    });
                                    dataTable.addRows(transformedData);

                                    var options = {
                                        hAxis: {
                                            title: 'Dia',
                                            format: '0'
                                        },
                                        vAxis: {
                                            title: 'Registros'
                                        },
                                        tooltip: {
                                            isHtml: true
                                        }
                                    };

                                    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
                                    chart.draw(dataTable, options);
                                },
                                error: function(error) {
                                    console.error('Erro ao obter dados: ', error);
                                }
                            });
                        }
                    </script>

                    <!--<div id="popup" class="popup">
                        <div class="divPopup">
                            <p>Sua plataforma se encontra com status <b class="bold-red">GGR IRREGULAR</b>, regularize o mais rápido possível para que os jogos continuem funcionando.</p>
                            <button class="buttonGGR" style="margin: 0 auto !important;" onclick="window.location.href='/adm/GGR';">REGULARIZAR GGR</button>
                        </div>
                    </div>

                     <script>
                        var status = "<?php echo $result_ggr2['status_ggr']; ?>";
                        if (status == "REGULAR") {
                            document.getElementById("popup").style.display = "none";
                        } else if (status == "IRREGULAR") {
                            document.getElementById("popup").style.display = "block";
                        }
                    </script> -->
                    <?php require '../components/footer.php' ?>
                </div>
            </div>
            <script src="assets/libs/jquery/dist/jquery.min.js"></script>
            <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
            <script src="assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
            <script src="assets/extra-libs/sparkline/sparkline.js"></script>
            <script src="dist/js/waves.js"></script>
            <script src="dist/js/sidebarmenu.js"></script>
            <script src="dist/js/custom.min.js"></script>
            <script src="assets/libs/flot/excanvas.js"></script>
            <script src="assets/libs/flot/jquery.flot.js"></script>
            <script src="assets/libs/flot/jquery.flot.pie.js"></script>
            <script src="assets/libs/flot/jquery.flot.time.js"></script>
            <script src="assets/libs/flot/jquery.flot.stack.js"></script>
            <script src="assets/libs/flot/jquery.flot.crosshair.js"></script>
            <script src="assets/libs/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
            <script src="dist/js/pages/chart/chart-page-init.js"></script>
</body>

</html>