<?php
try {
    include './../../connection.php';

    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $per_page = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 25;
    $search = $_POST['search'] ?? '';
    $offset = ($page - 1) * $per_page;
    $columns = $_POST['columns'];
    $orders = [];

    foreach ($_POST['order'] as $order) {
        $orders[] = $columns[$order['column']]['data'] . ' ' . $order['dir'];
    }

    $orders = implode(', ', $orders);

    $data = stmt(
        "SELECT a.id,
                   a.data_cadastro,
                   a.email,
                   a.telefone,
                   a.saldo,
                   a.bonus,
                   a.linkafiliado,
                   a.revenue_share,
                   a.depositou,
                   a.bloc,
                   a.saldo_cpa,
                   a.percas,
                   a.ganhos,
                   a.cpa,
                   a.cpafake,
                   a.comissaofake,
                   a.saldo_cpa,
                   a.sacou,
                   a.sacou_saldo,
                   a.saldo_rev,
                   (a.sacou_saldo + a.sacou) as sacou_total,
                   (a.saldo_cpa + a.saldo_rev - a.sacou) as valor,
                   IFNULL(a.origem, 'N/A') as origem,                                        
                   a.dificuldade,
                   a.coin_ind,
                   a.xmeta_ind,
                   a.saldo_fake,
                   a.leads_ativos as cads_ativo,
                   (SELECT count(*) FROM appconfig b WHERE b.lead_aff = a.id) as cads
            FROM appconfig a
            WHERE a.email LIKE ?
            ORDER BY $orders
            LIMIT ? OFFSET ?
            ", 'sii', ['%' . $search . '%', $per_page, $offset], false);

    // Calculate total number of records
    $totalRecords = stmt("SELECT COUNT(*) as count FROM appconfig")['count'];

    header('Content-Type: application/json');
    echo json_encode([
        'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        'recordsTotal' => $totalRecords, // total number of records in the table
        'recordsFiltered' => $totalRecords, // total number of records after filtering (in this case, it's the same as total records)
        'data' => $data // the data array
    ]);
} catch (Exception $e) {
    var_dump($e);
    http_response_code(200);
}