<?php
include_once 'connection.php';
$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

$sql = "SELECT * FROM app";
$result2 = $conn->query($sql);
$result = $result2->fetch_assoc();

$conn->close();
?>

<?php foreach (get_all('pixels', ['local' => 'header']) as $pixel) { ?>
    <?= file_get_contents($pixel['script']) ?>
<?php } ?>
