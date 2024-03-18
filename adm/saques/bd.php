<?php

function utf8_converter($array)
{
    array_walk_recursive($array, function (&$item, $key) {
        if (!mb_detect_encoding($item, 'utf-8', true)) {
            $item = utf8_encode($item);
        }
    });

    return $array;
}

try {
    include './../../connection.php';

    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $per_page = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 25;
    $search = $_POST['search'] ?? '';
    $offset = ($page - 1) * $per_page;

    $columns = $_POST['columns'] ?? [];

    // search column with data = 'status'
    $statusColumn = array_search('status', array_column($columns, 'data'));

    if ($columns) {
        $orders = [];
        foreach ($_POST['order'] as $order) {
            $orders[] = $columns[$order['column']]['data'] . ' ' . $order['dir'];
        }
        $orders = implode(', ', $orders);
    } else {
        $orders = 'created_at DESC';
    }

    if ($statusColumn !== false) {
        $statusSearch = $columns[$statusColumn]['search']['value'];

        if (!empty($statusSearch)) {
            $statusStmt = "AND status = '$statusSearch'";
        } else {
            $statusStmt = '';
        }
    } else {
        $statusStmt = '';
    }

    $data = stmt("SELECT *  FROM saques
             WHERE (email LIKE ? OR pix LIKE ?) $statusStmt
             ORDER BY $orders
             LIMIT ? OFFSET ?",
        'ssii', ['%' . $search . '%', '%' . $search . '%', $per_page, $offset], false);

    // Calculate total number of records
    $totalRecords = stmt("SELECT COUNT(*) as count FROM saques WHERE (email LIKE ? OR pix LIKE ?) ${statusStmt}", "ss", ['%' . $search . '%', '%' . $search . '%'])['count'];

    header('Content-Type: application/json');
    echo json_encode(utf8_converter([
        'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        'recordsTotal' => $totalRecords, // total number of records in the table
        'recordsFiltered' => $totalRecords, // total number of records after filtering (in this case, it's the same as total records)
        'data' => $data // the data array
    ]));
} catch (Exception $e) {
    http_response_code(200);
}
