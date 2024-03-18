<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$config = array(
    'db_host' => $_ENV['DB_HOST'],
    'db_user' => $_ENV['DB_USER'],
    'db_name' => $_ENV['DB_NAME'],
    'db_pass' => $_ENV['DB_PASS'],
);

function connect()
{
    global $config;
    $conn = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
    if (!$conn) {
        die('Could not connect: ' . mysqli_error($conn));
    }
    return $conn;
}

function close_connection($conn)
{
    mysqli_close($conn);
}

function stmt($sql, $types = [], $params = [], $unique = true)
{
    $conn = connect();
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    if (count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (is_bool($result)) {
        $data = $result;
    } else if (mysqli_num_rows($result) == 1 && $unique) {
        $data = mysqli_fetch_assoc($result);
    } else {
        $data = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($data, $row);
            }
        }
    }

    close_connection($conn);
    return $data;
}

function get_all($table, $where = [])
{
    $conn = connect();
    $sql = "SELECT * FROM $table";
    if (count($where) > 0) {
        $sql .= " WHERE ";
        $sql .= implode(" AND ", array_keys($where)) . " = ?";
    }
    $stmt = mysqli_prepare($conn, $sql);
    if (count($where) > 0) {
        mysqli_stmt_bind_param($stmt, str_repeat("s", count($where)), ...array_values($where));
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($data, $row);
        }
    }
    close_connection($conn);
    return $data;
}

function get_by_id($table, $id, $fields = [])
{
    if (count($fields) > 0) {
        $fields = implode(', ', $fields);
    } else {
        $fields = '*';
    }

    $sql = "SELECT " . $fields . " FROM $table WHERE id = ?";

    return stmt($sql, "i", [$id]);
}


function insert($table, $data)
{
    $sql = "INSERT INTO $table (";
    $sql .= implode(",", array_keys($data)) . ")";
    $sql .= " VALUES (";
    // implode ? for bind with statements
    $sql .= implode(",", str_split(str_repeat('?', count($data)))) . ")";

    return stmt($sql, str_repeat("s", count($data)), array_values($data));
}

function update($table, $data, $where)
{
    $sql = "UPDATE $table SET ";
    $sql .= implode(" = ?, ", array_keys($data)) . " = ?";
    if (count($where) > 0) {
        $sql .= " WHERE ";
        $sql .= implode(" AND ", array_keys($where)) . " = ?";
    }
    $params = array_merge(array_values($data), array_values($where));
    return stmt($sql, str_repeat("s", count($params)), $params);
}

function get_by($table, $data)
{
    $sql = "SELECT * FROM $table WHERE ";
    $sql .= implode(" AND ", array_keys($data)) . " = ?";

    return stmt($sql, str_repeat("s", count($data)), array_values($data));
}

function delete($table, $where)
{
    $sql = "DELETE FROM $table WHERE ";
    $sql .= implode(" AND ", array_keys($where)) . " = ?";
    return stmt($sql, str_repeat("s", count($where)), array_values($where));
}