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
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords" content="Admin Dashboard"/>
    <meta name="description" content="Admin Dashboard"/>
    <meta name="robots" content="noindex,nofollow"/>
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/<?php echo $_ENV['LOGOICON_DESENVOLVEDORA'];?>"/>
    <link href="../assets/libs/flot/css/float-chart.css" rel="stylesheet"/>
    <link href="../dist/css/style.min.css" rel="stylesheet"/>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css"/>
</head>

<body>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5">
                <a class="navbar-brand" href="../">
                    <span class="logo-text ms-2">
                      <img src="../assets/images/<?php echo $_ENV['LOGOTEXT_DESENVOLVEDORA'];?>" width="150" height="50" alt="homepage"
                           class="light-logo"/>
                    </span>
                </a>
                <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                    <i class="ti-menu ti-close"></i>
                </a>
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
        <style>
            #withdraw_filter_wrapper {
                display: flex;
                flex-direction: row;
                justify-content: start;
                align-items: center;
            }

            @media (max-width: 512px) {
                #withdraw_filter_wrapper {
                    justify-content: center;
                }

                #withdraw_filter_wrapper select {
                    max-width: 150px;
                }
            }

            td {
                font-size: 1rem;
                white-space: nowrap;
            }
        </style>
    </header>
    <?php include '../components/aside.php' ?>

    <div class="page-wrapper">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tabela de Depósitos</h5>
                <div class="table-responsive">
                    <table id="withdrawals-table" class="table table-striped table-bordered">
                        <div id="withdraw_filter_wrapper">
                            <label for="statusFilter" class="pr-1">Filtrar por status:</label>
                            <select id="statusFilter" class="mb-2"
                                    style="
                                border: 1px solid #aaa;
                                border-radius: 3px;
                                padding: 5px;
                                background-color: transparent;
                                color: inherit;
                                width: auto;
                            "
                            >
                                <option value="">Todos</option>
                                <option value="PAID_OUT">Pagos</option>
                                <option value="UNPAID">Não Pagos</option>
                                <option value="WAITING_FOR_APPROVAL">Aguardando Aprovação</option>
                            </select>
                        </div>
                        <thead>
                        <tr>
                            <th>Data / Hora</th>
                            <th>Email</th>
                            <th>Cod. Referência</th>
                            <th>Valor</th>
                            <th>Bônus</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody id="table-body">
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Data / Hora</th>
                            <th>Email</th>
                            <th>Cod. Referência</th>
                            <th>Valor</th>
                            <th>Bônus</th>
                            <th>Satus</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const moment = (data) => {
            return {
                format: (format) => {
                    return new Intl.DateTimeFormat('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }).format(new Date(data));
                }
            }
        }

        $(document).ready(function () {
            const table = new DataTable('#withdrawals-table', {
                serverSide: true,
                ordering: true,
                language: {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "_MENU_ resultados por página",
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": "Pesquisar",
                    "oPaginate": {
                        "sNext": "Próximo",
                        "sPrevious": "Anterior",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    },
                    "select": {
                        "rows": {
                            "_": "Selecionado %d linhas",
                            "0": "Nenhuma linha selecionada",
                            "1": "Selecionado 1 linha"
                        }
                    },
                    "buttons": {
                        "copy": "Copiar para a área de transferência",
                        "copyTitle": "Cópia bem sucedida",
                        "copySuccess": {
                            "1": "Uma linha copiada com sucesso",
                            "_": "%d linhas copiadas com sucesso"
                        }
                    }
                },
                ajax: {
                    url: 'bd.php',
                    type: 'POST',
                    data: function (d) {
                        d.page = d.start / d.length + 1; // calculate page number
                        d.per_page = d.length; // items per page
                        d.search = d.search.value; // search value
                    },
                    dataSrc: function (json) {
                        return data = json.data;
                    }
                },
                /**
                 responsive: {
                    details: {
                        type: 'column',
                        searchable: true,
                        target: 'tr',
                        renderer: function (api, rowIdx, columns) {
                            const data = $.map(columns, function (col) {
                                return col.hidden ?
                                    '<dl data-dtr-index="' + col.columnIndex + '" data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                    '<span class="dtr-title">' + col.title + ': </span> ' +
                                    '<span class="dtr-data">' + col.data + '</span>' +
                                    '</dl>' :
                                    '';
                            }).join('');

                            return data ? $('<div/>').append(data).append(`<button class="btn btn-primary btn-edit" data-id="${rowIdx}">Editar</button>`) : false;
                        }
                    }
                },
                 **/
                initComplete: function () {
                    table.columns().every(function () {
                        const that = this;

                        $('input', this.footer()).on('keyup change clear', function () {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('input[type="search"]')
                        .attr('placeholder', 'Pesquisar...')
                        .on('keyup', function () {
                            if ($(this).val().length > 3 || $(this).val().length === 0) {
                                table.search(this.value).draw();
                                console.log($(this).val().length);
                            }
                        });
                },
                columns: [
                    {
                        className: 'all',
                        data: 'data', render: function (data, type, row) {
                            return moment(row.data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    },
                    {data: 'email'},
                    {data: 'externalreference'},
                    {
                        data: 'valor', render: function (data, type, row) {
                            return row.valor.toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            });
                        }
                    },
                    {data: 'bonus'},
                    {
                        data: 'status', render: function (data, type, row) {
                            if (row.status == 'PAID_OUT') {
                                return 'Pago';
                            } else if (row.status == 'UNPAID') {
                                return 'Não Pago';
                            } else {
                                return 'Pendente';
                            }
                        },
                    },
                ],
            });

            $('#statusFilter').on('change', function () {
                console.log(table.column(5))
                table.column(5).search(this.value).draw()
            });
        });


    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <?php require '../../components/footer.php'?>
</div>
</div>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="../assets/extra-libs/sparkline/sparkline.js"></script>
<script src="../dist/js/waves.js"></script>
<script src="../dist/js/sidebarmenu.js"></script>
<script src="../dist/js/custom.min.js"></script>
<script src="../assets/extra-libs/multicheck/datatable-checkbox-init.js"></script>
<script src="../assets/extra-libs/multicheck/jquery.multicheck.js"></script>
</body>

</html>