<?php
$host = '127.0.0.1';
$dbname  = 'apicomphp';
$port = '5432';
$user = 'postgres';
$password = 'boeing';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
} catch (PDOException $e) {
    die(json_encode(['erro' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]));
}

?>