<?php
include './../../connection.php';

$conn = connect();

$sql = "SELECT ggr_24h FROM ggr";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo number_format($row["ggr_24h"], 2, '.', '');
} else {
    echo "0";
}

$conn->close();
http_response_code(200);
