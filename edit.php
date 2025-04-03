<?php
include "conexao.php";

// Verifica se um ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID inválido!";
    exit;
}

$id = $_GET['id'];

// Busca os dados do cliente no banco
$stmt = $conn->prepare("SELECT * FROM cliente WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    echo "Cliente não encontrado!";
    exit;
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];

    // Atualiza os dados no banco
    $stmt = $conn->prepare("UPDATE cliente SET nome = ?, telefone = ? WHERE id = ?");
    if ($stmt->execute([$nome, $telefone, $id])) {
        echo "Dados atualizados com sucesso!";
        echo "<br><a href='visualizar.php'>Voltar</a>";
        exit;
    } else {
        echo "Erro ao atualizar!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente</title>
</head>
<body>
    <h2>Editar Cliente</h2>
    <form method="post">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($cliente['nome']) ?>" required>
        <br>
        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" required>
        <br>
        <input type="submit" value="Salvar">
    </form>
    <br>
    <a href="visualizar.php">Cancelar</a>
</body>
</html>
