<?php
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $barbeiro_id = $_POST["barbeiro_id"];
    $data = $_POST["data"];
    $hora = $_POST["hora"];
    $servico = $_POST["servico"];

    // cliente ja existe?
    //veifica pelo telefone
    
    $stmt = $conn->prepare("SELECT id FROM cliente WHERE telefone = ?");
    $stmt->execute([$telefone]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        // Insere o novo cliente
        $stmt = $conn->prepare("INSERT INTO cliente (nome, telefone) VALUES (?, ?)");
        $stmt->execute([$nome, $telefone]);
        $cliente_id = $conn->lastInsertId();
    } else {
        $cliente_id = $cliente['id'];
    }

    // Inserir o agendamento na tabela agendamento_novo
    $stmt = $conn->prepare("INSERT INTO agendamento_novo (barbeiro_id, cliente_id, data, hora, servico) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$barbeiro_id, $cliente_id, $data, $hora, $servico])) {
        echo "Agendamento realizado com sucesso!";
    } else {
        echo "Erro ao agendar.";
    }
}
?>

<a href="edit.php?id=<?= $cliente['id'] ?>">alterar Agendamento</a>

<a href="excluir.php">Excluir Agendamento</a>
