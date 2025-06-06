<?php

require 'config.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
    try {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);

            $stmt = $pdo->prepare("SELECT * FROM items WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log(print_r($item, true)); // Isso vai para o log do PHP

            if ($item) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'sucesso',
                    'mensagem' => 'Item encontrado',
                    'dados' => $item
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'erro',
                    'mensagem' => 'Item não encontrado'
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }

        } else {
            $stmt = $pdo->query("SELECT * FROM items ORDER BY id ASC");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Itens encontrados',
                'dados' => $items
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Erro no servidor',
            'detalhes' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);

        // Validação dos campos obrigatórios
        $requiredFields = ['nome', 'descricao', 'valor', 'unidade', 'tipo_aplicacao'];
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'erro',
                    'mensagem' => "Campo obrigatório ausente: $field"]);
                exit;
            }
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO items (nome, descricao, valor, unidade, tipo_aplicacao)
                VALUES (:nome, :descricao, :valor, :unidade, :tipo_aplicacao)
            ");
            $stmt->execute([
                ':nome'           => $input['nome'],
                ':descricao'      => $input['descricao'],
                ':valor'          => $input['valor'],
                ':unidade'        => $input['unidade'],
                ':tipo_aplicacao' => $input['tipo_aplicacao']
            ]);
            http_response_code(201);
            echo json_encode([
                'status' => 'sucesso',
                'mensagem' => 'Item criado com o ID ' . $pdo->lastInsertId(),
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['erro' => 'Método não permitido']);
}

?>