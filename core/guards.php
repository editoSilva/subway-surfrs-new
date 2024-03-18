<?php

function regex($value, $name, $regex)
{
    if (!preg_match($regex, $value)) {
        return "O campo $name não é válido";
    }
    return null;
}

function required($value, $name)
{
    if ($value === null || $value === '') {
        return "O campo $name é obrigatório";
    }
    return null;
}

function email($value, $name)
{
    return regex($value, $name, "/^[a-z0-9_]+@[a-z0-9]+\.[a-z]+(\.[a-z]+)?$/i");
}

function password($value, $name)
{
    return regex($value, $name, "/^[a-z0-9]{6,}$/i");
}

function lmin($value, $name, $min)
{
    if (strlen($value) <= $min) {
        return "O campo $name deve ter no mínimo $min caracteres";
    }
    return null;
}

function lmax($value, $name, $max)
{
    if (strlen($value) >= $max) {
        return "O campo $name deve ter no máximo $max caracteres";
    }
    return null;
}

function equals($value, $name, $other)
{
    if ($value != $other) {
        return "O campo $name deve ser igual ao campo $other";
    }
    return null;
}

function unique($value, $name, $args)
{
    $table = $args[0];
    $column = $args[1];

    $conn = connect();
    $sql = "SELECT * FROM $table WHERE $column = '$value'";

    if (isset($args[2]) && $where = $args[2]) {
        if (is_array($where)) {
            // key => value
            $column = array_keys($where)[0];
            $value = array_values($where)[0];
        } else {
            $column = $name;
            $value = $where;
        }

        $sql .= " AND $column != '$value'";
    }

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return "O campo $name já está em uso";
    }
    return null;
}

function exists($value, $name, $args)
{
    if (empty($value)) {
        return null;
    }

    $table = $args[0];
    $column = $args[1];

    $conn = connect();
    $sql = "SELECT * FROM $table WHERE $column = '$value'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        return "O campo $name não existe";
    }
    return null;
}

function cpf($value, $name)
{
    if (empty($value)) {
        return null;
    }

    $cpf = preg_replace(' / [^0-9]/', '', (string)$value);

    // validade length
    if (strlen($cpf) != 11) {
        return "O campo $name não é válido";
    }

    // validade if all digits are the same
    if (preg_match(' / (\d)\1{10}/', $cpf)) {
        return "O campo $name não é válido";
    }

    // validate first digit
    for ($i = 9; $i < 11; $i++) {
        for ($j = 0, $k = 0; $k < $i; $k++) {
            $j += $cpf[$k] * (($i + 1) - $k);
        }
        $j = ((10 * $j) % 11) % 10;
        if ($cpf[$k] != $j) {
            return "O campo $name não é válido";
        }
    }

    return null;
}

function number($value, $name)
{
    if (empty($value)) {
        return null;
    }

    if (!is_numeric($value)) {
        return "O campo $name deve ser um número";
    }

    return null;
}

function in($value, $name, $values)
{
    if (empty($value)) {
        return null;
    }

    if (!in_array($value, $values)) {
        return "O campo $name não é válido";
    }

    return null;
}

function vmin($value, $name, $min)
{
    if (empty($value)) {
        return null;
    }

    if ($value < $min) {
        return "O campo $name deve ser maior ou igual a $min";
    }

    return null;
}

function vmax($value, $name, $max)
{
    if (empty($value)) {
        return null;
    }

    if ($value > $max) {
        return "O campo $name deve ser menor ou igual a $max";
    }

    return null;
}


function guard($data, $rules, $messages = [])
{
    /**
     * e.g
     * $data = [
     *    'name' => 'John Doe',
     *    'email' => ['unique', 'required', 'email', 'exists' => ['users', 'email']],
     *    'password' => ['required', 'password', 'max' => [20]],
     */

    $errors = [];

    foreach ($rules as $name => $rule) {
        if (is_array($rule)) {
            foreach ($rule as $key => $value) {
                if (is_numeric($key)) {
                    $error = $value($data[$name], $name);
                    $custom = $name . '.' . $value;
                } else {
                    $error = $key($data[$name], $name, $value);
                    $custom = $name . '.' . $key;
                }
                if ($error) {
                    if (isset($messages[$custom])) {
                        $errors[$name] = $messages[$custom];
                    } else {
                        $errors[$name] = $error;
                    }
                    break;
                }
            }
        } else {
            $error = $rule($data[$name], $name);
            if ($error) {
                $custom = $name . '.' . $rule;
                if (isset($messages[$custom])) {
                    $errors[$name] = $messages[$custom];
                } else {
                    $errors[$name] = $error;
                }
            }
        }
    }

    return $errors;
}
