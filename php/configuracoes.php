<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['barbeiro_id'])) {
    header("Location: login.html");
    exit;
}

$barbeiro_id = $_SESSION['barbeiro_id'];

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'];
    $abrir = isset($_POST['abrir']) ? 1 : 0;
    $horario_abertura = $_POST['horario_abertura'];
    $horario_fechamento = $_POST['horario_fechamento'];
    $motivo = $_POST['motivo'];
    $fechado = isset($_POST['fechado']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO configuracoes_barbeiro 
        (barbeiro_id, data, abrir, horario_abertura, horario_fechamento, motivo, fechado)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $barbeiro_id,
        $data,
        $abrir,
        $horario_abertura,
        $horario_fechamento,
        $motivo,
        $fechado
    ]);

    echo "<p style='color: green;'>Configuração salva com sucesso!</p>";
}

// Buscar configurações já salvas
$stmt = $conn->prepare("SELECT * FROM configuracoes_barbeiro WHERE barbeiro_id = ? ORDER BY data DESC");
$stmt->execute([$barbeiro_id]);
$configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Configurações da Barbearia</title>
</head>
<body>
    <h1>Configurações da Barbearia</h1>

    <form method="POST">
        <label>Data:</label>
        <input type="date" name="data" required><br><br>

        <label>Abrir nesse dia?</label>
        <input type="checkbox" name="abrir"><br><br>

        <label>Horário de Abertura:</label>
        <input type="time" name="horario_abertura"><br><br>

        <label>Horário de Fechamento:</label>
        <input type="time" name="horario_fechamento"><br><br>

        <label>Fechar esse dia?</label>
        <input type="checkbox" name="fechado"><br><br>

        <label>Motivo (opcional):</label>
        <input type="text" name="motivo"><br><br>

        <button type="submit">Salvar Configuração</button>
    </form>

    <hr>

    <h2>Configurações Salvas</h2>

    <?php if ($configs): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Data</th>
                <th>Abrir</th>
                <th>Horário Abertura</th>
                <th>Horário Fechamento</th>
                <th>Fechado</th>
                <th>Motivo</th>
            </tr>
            <?php foreach ($configs as $conf): ?>
                <tr>
                    <td><?= htmlspecialchars($conf['data']) ?></td>
                    <td><?= $conf['abrir'] ? 'Sim' : 'Não' ?></td>
                    <td><?= htmlspecialchars($conf['horario_abertura']) ?></td>
                    <td><?= htmlspecialchars($conf['horario_fechamento']) ?></td>
                    <td><?= $conf['fechado'] ? 'Sim' : 'Não' ?></td>
                    <td><?= htmlspecialchars($conf['motivo']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhuma configuração encontrada.</p>
    <?php endif; ?>
</body>
</html>
