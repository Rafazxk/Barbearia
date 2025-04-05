<?php
include "conexao.php";

session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['barbeiro_id'])) {
    header("Location: login.html");
    exit;
}

$barbeiro_id = $_SESSION['barbeiro_id'];
$barbeiro_id = isset($_GET['id']) ? $_GET['id'] : null;

// Lista de barbeiros
$stmt = $conn->prepare("SELECT * FROM barbeiro");
$stmt->execute();
$barbeiros = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($barbeiro_id) {
    // Agendamentos
    $stmt = $conn->prepare("SELECT a.id, c.nome AS cliente_nome, a.data, a.hora, s.nome AS servico, s.preco 
                            FROM agendamento_novo a
                            JOIN cliente c ON a.cliente_id = c.id
                            JOIN servico s ON a.servico = s.id
                            WHERE a.barbeiro_id = ? ORDER BY a.data, a.hora");
    $stmt->execute([$barbeiro_id]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ganhos totais
    $stmt = $conn->prepare("SELECT SUM(s.preco) AS total 
                            FROM agendamento_novo a
                            JOIN servico s ON a.servico = s.id
                            WHERE a.barbeiro_id = ?");
    $stmt->execute([$barbeiro_id]);
    $ganhos_totais = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Ganhos do dia
    $stmt = $conn->prepare("SELECT SUM(s.preco) AS total 
                            FROM agendamento_novo a
                            JOIN servico s ON a.servico = s.id
                            WHERE a.barbeiro_id = ? AND a.data = date('now')");
    $stmt->execute([$barbeiro_id]);
    $ganhos_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Ganhos da semana (últimos 7 dias)
    $stmt = $conn->prepare("SELECT SUM(s.preco) AS total 
                            FROM agendamento_novo a
                            JOIN servico s ON a.servico = s.id
                            WHERE a.barbeiro_id = ? AND a.data >= date('now', '-7 days')");
    $stmt->execute([$barbeiro_id]);
    $ganhos_semana = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Ganhos do mês (últimos 30 dias)
    $stmt = $conn->prepare("SELECT SUM(s.preco) AS total 
                            FROM agendamento_novo a
                            JOIN servico s ON a.servico = s.id
                            WHERE a.barbeiro_id = ? AND a.data >= date('now', '-30 days')");
    $stmt->execute([$barbeiro_id]);
    $ganhos_mes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} else {
    $agendamentos = [];
    $ganhos_totais = $ganhos_dia = $ganhos_semana = $ganhos_mes = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos por Barbeiro</title>
    <link rel="stylesheet" href="../styles/barbeiro.css">
</head>
<body>
    <header>
<nav class="menu">
    <h2>Agendamentos por Barbeiro</h2>
    <ul>
        <li><a href="barbeiro.php">Agendamentos</a></li>
        <li><a href="clientes.php">Clientes</a></li>
        <li><a href="configuracoes.php">Configurar Barbearia</a></li>
        <li><a href="logout.php">Sair</a></li>
    </ul>
</nav>
</header>

    <form method="get" action="barbeiro.php">
        <label for="barbeiro">Escolha o Barbeiro:</label>
        <select name="id" id="barbeiro">
            <option value="">Todos</option>
            <?php foreach ($barbeiros as $barbeiro): ?>
                <option value="<?= $barbeiro['id'] ?>" <?= $barbeiro['id'] == $barbeiro_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($barbeiro['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>

    <?php if ($barbeiro_id): ?>
        <h3>Ganhos:</h3>
        <ul>
            <li><strong>Hoje:</strong> R$ <?= number_format($ganhos_dia, 2, ',', '.') ?></li>
            <li><strong>Últimos 7 dias:</strong> R$ <?= number_format($ganhos_semana, 2, ',', '.') ?></li>
            <li><strong>Últimos 30 dias:</strong> R$ <?= number_format($ganhos_mes, 2, ',', '.') ?></li>
            <li><strong>Total:</strong> R$ <?= number_format($ganhos_totais, 2, ',', '.') ?></li>
        </ul>

        <h3>Agendamentos:</h3>
        <?php if (count($agendamentos) > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Serviço</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agendamentos as $ag): ?>
                        <tr>
                            <td><?= htmlspecialchars($ag['cliente_nome']) ?></td>
                            <td><?= $ag['data'] ?></td>
                            <td><?= $ag['hora'] ?></td>
                            <td><?= htmlspecialchars($ag['servico']) ?></td>
                            <td>R$ <?= number_format($ag['preco'], 2, ',', '.') ?></td>
                            <td>
                                <a href="editar_agendamento.php?id=<?= $ag['id'] ?>">Editar</a> |
                                <a href="excluir_agendamento.php?id=<?= $ag['id'] ?>" onclick="return confirm('Deseja realmente excluir este agendamento?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum agendamento encontrado para esse barbeiro.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
