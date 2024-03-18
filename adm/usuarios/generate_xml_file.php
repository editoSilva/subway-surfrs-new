<?php

require './../../vendor/autoload.php';
include_once './../../connection.php';

$conn = connect();

function load_users()
{
    return stmt('SELECT email, telefone from appconfig', '', []);
}

// Create a new DOMDocument instance
$dom = new DOMDocument('1.0', 'UTF-8');

$root = $dom->createElement('root');
$dom->appendChild($root);

$users = load_users();

foreach ($users as $user) {
    try {
        $userElement = $dom->createElement('user');
        $root->appendChild($userElement);

        $email = $dom->createElement('email', $user['email']);
        $userElement->appendChild($email);

        // format telefone
        // remove non-numeric characters
        $telefone = preg_replace('/\D/', '', $user['telefone']);
        // remove leading 0
        $telefone = ltrim($telefone, '0');
        // reformats telefone
        $telefone = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);

        $telefone = $dom->createElement('telefone', $telefone);

        $userElement->appendChild($telefone);
    } catch (Exception $e) {
        var_dump($e);
        exit;
    }
}

// Generate XML
$xml = $dom->saveXML();

$filename = 'users.xml';

// Set headers to force download
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output the XML content
echo $xml;
exit;
?>
