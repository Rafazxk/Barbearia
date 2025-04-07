<?php

include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['id'])) {

   
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $servico = $_POST['servico_id'] ?? null;  
    $barbeiro_id = $_POST['barbeiro_id'] ?? null;

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
    $stmt = $conn->prepare("SELECT a.id, a.data, a.hora, s.nome AS servico, b.nome AS barbeiro, c.nome AS cliente, c.telefone
    FROM agendamento_novo a
    JOIN servico s ON a.servico = s.id
    JOIN barbeiro b ON a.barbeiro_id = b.id
    JOIN cliente c ON a.cliente_id = c.id
    WHERE a.id = ?");

    $stmt->execute([$id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($agendamento) {
        $agendamento_id = $agendamento['id'];
        $nome = $agendamento['cliente'];
        $telefone = $agendamento['telefone'];
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
            <link rel="stylesheet" href="../styles/concluido.css">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
        </head>
        <body>
            <div class="card">
                <h1>Agendamento Confirmado!</h1>
                <p><strong>Nome:</strong> <?= isset($nome) ? htmlspecialchars($nome) : 'Não informado' ?></p>
                <p><strong>Telefone:</strong> <?= $telefone ?> </p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($data)) ?></p>
                <p><strong>Hora:</strong> <?= $hora ?></p>
                <p><strong>Serviço:</strong> <?= $servico ?></p>
                <p><strong>Barbeiro:</strong> <?= $barbeiro ?></p>
                <a href="agendamento.php">Voltar para Agendamento</a>
            </div>
        </body>
        </html>

   
