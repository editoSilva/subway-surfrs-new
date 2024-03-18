<?php
session_start();

if (!isset($_SESSION['emailadm'])) {
    $url = $_ENV['BASE_URL'];
    header("Location: $url/adm/login");
    exit();
}

require './../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('./../../');
$dotenv->safeLoad();
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
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/<?php echo $_ENV['FAVICON_DESENVOLVEDORA'];?>"/>
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
    <?php include '../components/aside.php' ?>
    <script>
        const downloadXMLUsers = () => {
            fetch('generate_xml_file.php')
                .then(response => response.text())
                .then(data => {
                    console.log(data)
                    let downloadLink = document.createElement('a');
                    downloadLink.href = 'data:text/xml;charset=utf-8,' + encodeURIComponent(data);
                    downloadLink.download = 'file.xml';

                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                })
                .catch(error => console.error(error));

        }
    </script>

    <style>
        .subDescricao {
            font-size: 0.8rem;
            color: #999;
            text-align: justify;
            display: block;
        }

        .btn-xml-wrapper {
            display: flex;
            justify-content: flex-end !important;
            margin-bottom: 20px;
            width: 100%;
        }

        .btn-xml {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
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

        #user-table_length > label > select {
            max-width: 50px;
        }

        #user-table_length > label {
            display: flex;
            align-items: center;
            gap: 10px
        }

        th, td {
            white-space: nowrap;
        }

        .pagination li {
            padding: 0 !important;
            margin: 0;
        }


    </style>

    <div class="page-wrapper">
        <?php require '../components/messages.php' ?>
        <div class="card">
            <div class="card-body">
                <div class="btn-xml-wrapper">
                    <button class="btn-xml" onclick="downloadXMLUsers()">Baixar XML</button>
                </div>
                <h5 class="card-title">Tabela de Usuários</h5>
                <div class="table-responsive">
                    <table id="user-table" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Email</th>
                            <th>Saldo Real</th>
                            <th>Saldo Fake</th>
                            <th>Total Depositado</th>
                            <th>Total Sacado</th>
                            <th>Data/Hora</th>
                            <th>Cadastros Ativos</th>
                            <th>Telefone</th>
                            <th>Origem</th>
                            <th>Valor Disponivel (Afiliado)</th>
                            <th>Cadastros Totais</th>
                            <th>Dificuldade</th>
                            <th>Meta Individual</th>
                            <th>Moeda Individual</th>
                        </tr>
                        </thead>
                        <tbody id="table-body">
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Email</th>
                            <th>Saldo Real</th>
                            <th>Saldo Fake</th>
                            <th>Total Depositado</th>
                            <th>Total Sacado</th>
                            <th>Data/Hora</th>
                            <th>Cadastros Ativos</th>
                            <th>Telefone</th>
                            <th>Origem</th>
                            <th>Valor Disponivel (Afiliado)</th>
                            <th>Cadastros Totais</th>
                            <th>Dificuldade</th>
                            <th>Meta Individual</th>
                            <th>Moeda Individual</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuário</h5>
                    <button type="button" class="btn-close" onclick="closeEditUser()" data-dismiss="editModal"
                            aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form action="update.php" method="post">
                        <input type="hidden" id="editUserId" name="id">
                        <br/>

                        <label for="editEmail">Email:
                            <span class="subDescricao">Somente visualização</span>
                        </label>
                        <input readonly type="text" class="form-control" id="editEmail" name="email">
                        <br/>

                        <label for="editTelefone">Telefone:
                            <span class="subDescricao">Somente visualização</span>
                        </label>
                        <input readonly type="text" class="form-control" id="editTelefone" name="telefone">
                        <br/>

                        <label for="editSaldo">Saldo Real:
                            <span class="subDescricao">Saldo depositado.</span>
                        </label>
                        <input type="text" class="form-control" id="editSaldo" name="saldo">
                        <br/>

                        <label for="editSaldoFake">Saldo Fake:
                            <span class="subDescricao">
                                Saldo que o usuário vê, mas não é real. Ao exibir o saldo, o sistema irá somar o saldo real com o saldo fake. É possível
                                ganhar saldo fake ao jogar, mas não é possível sacar o saldo fake.
                            </span>
                        </label>
                        <input type="text" class="form-control" id="editSaldoFake" name="saldo_fake">
                        <br/>

                        <label for="editRevenueShare">Revenue Share (%):
                            <span class="subDescricao">Valor entre 0 e 100</span>
                        </label>
                        <input type="text" class="form-control" id="editRevenueShare" name="revenue_share">
                        <br/>

                        <label for="editCpa">CPA (R$):</label>
                        <input type="text" class="form-control" id="editCpa" name="cpa">
                        <br/>

                        <label for="editCpaFake">Chance do Afiliado Receber CPA (%):
                            <span class="subDescricao">Valor entre 0 e 100</span>
                        </label>
                        <input type="text" class="form-control" id="editCpaFake" name="cpafake">
                        <br/>

                        <label for="editComissaoFake">Porcentagem de Rev. Share Falso (%):
                            <span class="subDescricao">Valor entre 0 e 100</span>
                        </label>
                        <input type="text" class="form-control" id="editComissaoFake" name="comissaofake">
                        <br/>
                        <label for="editLinkAfiliado">Link de Afiliado:
                            <span class="subDescricao">Somente visualização</span>
                        </label>
                        <input readonly type="text" class="form-control" id="editLinkAfiliado" name="linkafiliado">
                        <br/>

                        <label for="editDificuldade">Dificuldade no jogo:
                            <span class="subDescricao">
                                Altera a dificuldade no jogo para este usuário. Se a dificuldade for diferente de "Dificuldade global", o usuário
                                terá uma dificuldade diferente dos outros usuários. Se a dificuldade for "Dificuldade global", o usuário terá a
                                mesma dificuldade definida no painel de configuração.
                            </span>
                        </label>
                        <select name="dificuldade" id="editDificuldade" class="form-select custom-input"
                                aria-label="Escolha a dificuldade">
                            <option value="nenhuma">Dificuldade global</option>
                            <option value="facil">Fácil</option>
                            <option value="medio">Médio</option>
                            <option value="dificil">Difícil</option>
                            <option value="impossivel">Impossível</option>
                        </select>
                        <br/>
                        <label for="editCoinInd">Valor da Moeda Individual:
                            <span class="subDescricao">
                                Valor da moeda para o usuário sendo editado. Caso o valor seja '0' o valor setado será o valor global.
                            </span>
                        </label>
                        <input type="text" class="form-control" id="editCoinInd" name="coinInd">
                        <br/>
                        <label for="editMetaInd">Meta Individual:
                            <span class="subDescricao">
                                Meta para o usuário encerrar a aposta no jogo. Caso o valor seja '0' o valor setado será o valor global.
                            </span>
                        </label>
                        <input type="text" class="form-control" id="editMetaInd" name="metaInd">
                        <br/>
                        <label for="editBloqueado" class="d-flex align-items-center gap-2">
                            Bloquear: <input type="checkbox" id="editBloqueado" name="bloqueado">
                        </label>
                        <span class="subDescricao">
                            Bloqueia o usuário para que ele não possa mais efetuar login.
                        </span>
                        <br/>
                        <div class="d-flex justify-content-between align-items-center p-0 m-0">
                            <button type="submit" class="btn btn-success">Salvar Alterações</button>
                            <button class="btn btn-primary" onclick="closeEditUser(); openEditPassword();"
                                    type="button">Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editPassword" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuário</h5>
                    <button type="button" class="btn-close" onclick="closeEditPassword()" data-dismiss="editModal"
                            aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form action="update_password.php" method="post">
                        <input type="hidden" id="editPasswordId" name="id">
                        <br/>

                        <label for="password">Senha:</label>
                        <input type="password" class="form-control" name="password">
                        <br/>

                        <label for="password_confirmation">Confirmar senha:</label>
                        <input type="password" class="form-control" name="password_confirmation">
                        <br/>

                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function openEditUser() {
            $('#editModal').modal('show')
        }

        function closeEditUser() {
            $('#editModal').modal('hide')
        }

        function openEditPassword() {
            $('#editPassword').modal('show')
        }

        function closeEditPassword() {
            $('#editPassword').modal('hide')
        }

        let data = [];

        $(document).ready(function () {
            const table = new DataTable('#user-table', {
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
                responsive: {
                    details: {
                        type: 'column',
                        searchable: true,
                        target: 'tr',
                        renderer: function (api, rowIdx, columns) {
                            const data = $.map(columns, function (col, i) {
                                return col.hidden ?
                                    '<dl data-dtr-index="' + col.columnIndex + '" data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                    '<span class="dtr-title">' + col.title + ': </span> ' +
                                    '<span class="dtr-data">' + col.data + '</span>' +
                                    '</dl>' :
                                    '';
                            }).join('');

                            return data ? $('<div/>')
                                .append(data)
                                .append(`<button class="btn btn-primary btn-edit" data-id="${rowIdx}">Editar</button>`) : false;
                        }
                    }
                },
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

                    $('input[type="search"]').attr('placeholder', 'Pesquisar...');
                    $('input[type="search"]').on('keyup', function () {
                        if ($(this).val().length > 3 || $(this).val().length === 0) {
                            table.search(this.value).draw();
                        }
                    });
                },
                columns: [

                    {
                        className: 'dtr-control',
                        data: 'email',
                        defaultContent: '',
                    },
                    {
                        data: 'saldo',
                        render: function (data, type, row) {
                            return row.saldo.toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            });
                        }
                    },
                    {
                        data: 'saldo_fake',
                        render: function (data, type, row) {
                            return row.saldo_fake.toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            });
                        }
                    },
                    {
                        data: 'depositou',
                        render: function (data, type, row) {
                            return row.depositou.toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            });
                        }
                    },
                    {
                        data: 'sacou_total',
                        render: function (data, type, row) {
                            return row.sacou_total.toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            });
                        }
                    },
                    {data: 'data_cadastro'},
                    {data: 'cads_ativo'},
                    {data: 'telefone'},
                    {data: 'origem'},
                    {
                        data: 'valor', render: function (data, type, row) {
                            return row.valor.toLocaleString('pt-br', {
                                style: 'currency',
                                currency: 'BRL'
                            });
                        }
                    },
                    {data: 'cads'},
                    {
                        data: 'dificuldade', render: function (data, type, row) {
                            if (row.dificuldade === 'nenhuma') {
                                return 'Dificuldade global';
                            }

                            return row.dificuldade.charAt(0).toUpperCase() + row.dificuldade.slice(1);
                        }
                    },
                    {data: 'xmeta_ind'},
                    {data: 'coin_ind'},
                ],

            });

            table.on('click', 'td.dt-control', function (e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                } else {
                    // Open this row
                    row.child(formatMoreOption(row.data())).show();
                    tr.classList.add("child")
                }
            });


            $('#user-table tbody').on('click', '.btn-edit', function () {
                const userId = $(this).data('id');
                console.log(userId)
                fillModals(userId);
                openEditUser();
            });

            function fillModals(index) {
                const user = data[index]

                $('#editEmail').val(user.email);
                $('#editTelefone').val(user.telefone);
                $('#editSaldo').val(user.saldo);
                $('#editSaldoFake').val(user.saldo_fake);
                $('#editLinkAfiliado').val(user.linkafiliado);
                $('#editRevenueShare').val(user.revenue_share);
                $('#editDepositou').val(user.depositou);
                $('#editBloqueado').prop('checked', user.bloc === 1);
                $('#editPerdas').val(user.percas);
                $('#editGanhos').val(user.ganhos);
                $('#editCpa').val(user.cpa);
                $('#editCpaFake').val(user.cpafake);
                $('#editComissaoFake').val(user.comissaofake);
                $('#editUserId').val(user.id);
                $('#editPasswordId').val(user.id);
                $('#editDificuldade').val(user.dificuldade);
                $('#editMetaInd').val(user.xmeta_ind);
                $('#editCoinInd').val(user.coin_ind);
            }
        });
    </script>

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
<script src="../assets/extra-libs/multicheck/datatable-checkbox-init.js"></script>
<script src="../assets/extra-libs/multicheck/jquery.multicheck.js"></script>
</body>
</html>