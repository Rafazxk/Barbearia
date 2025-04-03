<?php
include "conexao.php";

// Verifica se um ID de barbeiro foi passado
$barbeiro_id = isset($_GET['id']) ? $_GET['id'] : null;

// Obtém todos os barbeiros para exibir no filtro
$stmt = $conn->prepare("SELECT * FROM barbeiro");
$stmt->execute();
$barbeiros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se um barbeiro foi selecionado, mostra os agendamentos dele
if ($barbeiro_id) {
    $stmt = $conn->prepare("SELECT a.id, c.nome AS cliente_nome, a.data, a.hora, s.nome AS servico, s.preco 
                            FROM agendamento_novo a
                            JOIN cliente c ON a.cliente_id = c.id
                            JOIN servico s ON a.servico = s.id
                            WHERE a.barbeiro_id = ? ORDER BY a.data, a.hora");
    $stmt->execute([$barbeiro_id]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcula os ganhos totais do barbeiro
    $stmt = $conn->prepare("SELECT SUM(s.preco) AS ganhos_totais
                            FROM agendamento_novo a
                            JOIN servico s ON a.servico = s.id
                            WHERE a.barbeiro_id = ?");
    $stmt->execute([$barbeiro_id]);
    $ganhos_totais = $stmt->fetch(PDO::FETCH_ASSOC)['ganhos_totais'];
} else {
    // Se não for selecionado nenhum barbeiro, não exibe agendamentos
    $agendamentos = [];
    $ganhos_totais = 0;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos por Barbeiro</title>
    <link rel="stylesheet" href="styles/barbeiro.css">
</head>
<body>
    <h2>Agendamentos por Barbeiro</h2>

    <!-- Filtro de barbeiros -->
    <form method="get" action="barbeiro.php">
        <label for="barbeiro">Escolha o Barbeiro:</label>
        <select name="id" id="barbeiro">
            <option value="">Todos</option>
            <?php foreach ($barbeiros as $barbeiro): ?>
                <option value="<?= $barbeiro['id'] ?>" <?= $barbeiro['id'] == $barbeiro_id ? 'selected' : '' ?>>
                    <?= $barbeiro['nome'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>

    <?php if ($barbeiro_id): ?>
        <h3>Ganhos Totais: R$ <?= number_format($ganhos_totais, 2, ',', '.') ?></h3>

        <!-- Tabela de agendamentos -->
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Serviço</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($agendamentos)): ?>
                    <tr><td colspan="6">Nenhum agendamento encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?= htmlspecialchars($agendamento['cliente_nome']) ?></td>
                            <td><?= date("d/m/Y", strtotime($agendamento['data'])) ?></td>
                            <td><?= htmlspecialchars($agendamento['hora']) ?></td>
                            <td><?= htmlspecialchars($agendamento['servico']) ?></td>
                            <td>R$ <?= number_format($agendamento['preco'], 2, ',', '.') ?></td>
                            <td>
                                <!-- Botões de Alterar e Excluir -->
                                <a href="edit_agendamento.php?id=<?= $agendamento['id'] ?>">Alterar</a> |
                                <a href="excluir_agendamento.php?id=<?= $agendamento['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este agendamento?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
