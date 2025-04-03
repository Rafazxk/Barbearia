<?php
include "conexao.php";

// Verifica se um ID foi passado pela URL e se é numérico
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID inválido!";
    exit;
}

$id = $_GET['id'];

// Prepara e executa a exclusão
$stmt = $conn->prepare("DELETE FROM cliente WHERE id = ?");
if ($stmt->execute([$id])) {
 header("Location: visualizar.php");
   exit;
} else {
    echo "Erro ao excluir o cliente.";
}
?>
