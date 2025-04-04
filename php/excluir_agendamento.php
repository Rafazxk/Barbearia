<?php
include "conexao.php";

// Verifica se um ID de agendamento foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID inválido!";
    exit;
}

$id = $_GET['id'];

// Exclui o agendamento
$stmt = $conn->prepare("DELETE FROM agendamento_novo WHERE id = ?");
if ($stmt->execute([$id])) {
    echo "Agendamento excluído com sucesso!";
    echo "<br><a href='barbeiro.php'>Voltar</a>";
} else {
    echo "Erro ao excluir agendamento!";
}
?>
