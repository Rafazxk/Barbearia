<?php
include "conexao.php";

// Verifica se um ID de agendamento foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID inválido!";
    exit;
}

$id = $_GET['id'];

// Obtém os dados do agendamento
$stmt = $conn->prepare("SELECT a.id, c.nome AS cliente_nome, a.data, a.hora, s.nome AS servico, s.preco 
                        FROM agendamento_novo a
                        JOIN cliente c ON a.cliente_id = c.id
                        JOIN servico s ON a.servico = s.id
                        WHERE a.id = ?");
$stmt->execute([$id]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    echo "Agendamento não encontrado!";
    exit;
}

// Se o formulário for enviado, atualiza os dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_nome = $_POST['cliente_nome'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $servico_id = $_POST['servico_id'];

    // Atualiza o agendamento no banco
    $stmt = $conn->prepare("UPDATE agendamento_novo SET data = ?, hora = ?, servico = ? WHERE id = ?");
    if ($stmt->execute([$data, $hora, $servico_id, $id])) {
        echo "Agendamento atualizado com sucesso!";
        if (isset($agendamento['barbeiro_id'])) {
            echo "<br><a href='concluido.php?id=".$id."'>Voltar</a>";
            header("Location: concluido.php?id=$id");
exit;
        } else {
            echo "<br><a href='concluido.php?id=".$id."'>Voltar</a>";
        }
        exit;
    } else {
        echo "Erro ao atualizar!";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="../styles/edit.css">   
</head>
<body>
    <h2>Editar Agendamento</h2>
    <form method="post">
        <label for="cliente_nome">Nome do Cliente:</label>
        <input type="text" name="cliente_nome" value="<?= htmlspecialchars($agendamento['cliente_nome']) ?>" required>
        <br><br>

        <label for="data">Data:</label>
        <input type="date" name="data" value="<?= htmlspecialchars($agendamento['data']) ?>" required>
        <br><br>

        <label for="hora">Hora:</label>
        <input type="time" name="hora" value="<?= htmlspecialchars($agendamento['hora']) ?>" required>
        <br><br>

        <label for="servico_id">Serviço:</label>
        <select name="servico_id" required>
            <?php
            // Recupera todos os serviços
            $stmt = $conn->prepare("SELECT * FROM servico");
            $stmt->execute();
            $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($servicos as $servico):
                $selected = $agendamento['servico'] == $servico['id'] ? 'selected' : '';
                ?>
                <option value="<?= $servico['id'] ?>" <?= $selected ?>>
                    <?= $servico['nome'] ?> - R$ <?= number_format($servico['preco'], 2, ',', '.') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>
