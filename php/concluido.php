<?php

include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['id'])) {
   
    $nome = $_POST["nome"];
  // $telefone = $_POST["telefone"]; 
    $barbeiro_id = $_POST["barbeiro_id"];
    $data = $_POST["data"];
    $hora = $_POST["hora"];
    $servico = $_POST["servico"];

    // Verifica cliente
    $stmt = $conn->prepare("SELECT id FROM cliente WHERE telefone = ?");
    $stmt->execute([$telefone]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        $stmt = $conn->prepare("INSERT INTO cliente (nome, telefone) VALUES (?, ?)");
        $stmt->execute([$nome, $telefone]);
        $cliente_id = $conn->lastInsertId();
    } else {
        $cliente_id = $cliente['id'];
    }

    // Insere agendamento
    $stmt = $conn->prepare("INSERT INTO agendamento_novo (barbeiro_id, cliente_id, data, hora, servico) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$barbeiro_id, $cliente_id, $data, $hora, $servico]);

    // Redireciona para si mesmo com o ID
    $agendamento_id = $conn->lastInsertId();
    header("Location: concluido.php?id=$agendamento_id");
    exit;
}

// Exibe os dados do agendamento (se ID estiver definido)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT a.id, a.data, a.hora, s.nome AS servico, b.nome AS barbeiro
                            FROM agendamento_novo a
                            JOIN servico s ON a.servico = s.id
                            JOIN barbeiro b ON a.barbeiro_id = b.id
                            WHERE a.id = ?");
    $stmt->execute([$id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($agendamento) {
        $agendamento_id = $agendamento['id'];
        $data = $agendamento['data'];
        $hora = $agendamento['hora'];
        $servico = $agendamento['servico'];
        $barbeiro = $agendamento['barbeiro'];
    } else {
        echo "Agendamento não encontrado!";
        exit;
    }
}
?>

 <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <title>Agendamento Concluído</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f5f5f5;
                    padding: 20px;
                    text-align: center;
                }
                .card {
                    background-color: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                    display: inline-block;
                }
                h1 {
                    color: #27ae60;
                }
                p {
                    margin: 8px 0;
                }
                a {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    background-color: #3498db;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Agendamento Confirmado!</h1>
                <p><strong>ID:</strong> <?= $agendamento_id ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($data)) ?></p>
                <p><strong>Hora:</strong> <?= $hora ?></p>
                <p><strong>Serviço:</strong> <?= $servico ?></p>
                <p><strong>Barbeiro:</strong> <?= $barbeiro ?></p>
                <a href="agendamento.php">Voltar para Agendamento</a>
            </div>
        </body>
        </html>

   
