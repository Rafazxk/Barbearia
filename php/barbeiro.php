<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['barbeiro_id'])) {
    header("Location: login.html");
    exit;
}

$barbeiro_id = $_SESSION['barbeiro_id'];

// escolher barbeiro via get
$barbeiro_id = isset($_GET['id']) ? $_GET['id'] : $barbeiro_id;

// puxa todos os barbeiros
$stmt = $conn->prepare("SELECT * FROM barbeiro");
$stmt->execute();
$barbeiros = $stmt->fetchAll(PDO::FETCH_ASSOC);

//  busca os agendamentos e ganhos
if ($barbeiro_id) {
    $data_inicio = isset($_GET['inicio']) ? $_GET['inicio'] : date('Y-m-d');
    $data_fim = isset($_GET['fim']) ? $_GET['fim'] : date('Y-m-d');

    // Consulta de agendamentos
    $query = "SELECT a.id, c.nome AS cliente_nome, a.data, a.hora, s.nome AS servico, s.preco 
              FROM agendamento_novo a
              JOIN cliente c ON a.cliente_id = c.id
              JOIN servico s ON a.servico = s.id
              WHERE a.barbeiro_id = ?";

    $params = [$barbeiro_id];

    if ($data_inicio && $data_fim) {
        $query .= " AND a.data BETWEEN ? AND ?";
        $params[] = $data_inicio;
        $params[] = $data_fim;
    }

    $query .= " ORDER BY a.data, a.hora";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta de ganhos totais
    $ganhosQuery = "SELECT SUM(s.preco) AS ganhos_totais
                    FROM agendamento_novo a
                    JOIN servico s ON a.servico = s.id
                    WHERE a.barbeiro_id = ?";
    $ganhosParams = [$barbeiro_id];

    if ($data_inicio && $data_fim) {
        $ganhosQuery .= " AND a.data BETWEEN ? AND ?";
        $ganhosParams[] = $data_inicio;
        $ganhosParams[] = $data_fim;
    }

    $stmt = $conn->prepare($ganhosQuery);
    $stmt->execute($ganhosParams);
    $ganhos = $stmt->fetch(PDO::FETCH_ASSOC);
    $ganhos_totais = $ganhos['ganhos_totais'] ?? 0;
} else {
    // Caso nenhum barbeiro seja selecionado
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
    <link rel="stylesheet" href="../styles/barbeiro.css">
    
    <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap"
    rel="stylesheet">
</head>
<body>
<header>
    <h1>Painel <span id="tk">TK</span></h1>
    <nav>

    <a href="configuracoes.php?barbeiro_id=<?= $barbeiro_id
            ?>">Configurações</a>
      <a href="logout.php" id="sair">Sair</a>
        
    </nav>
</header>


    <!-- Filtro de barbeiros -->
<div class="escolha">
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
</div>
<div class="ganhos">
    <?php if ($barbeiro_id): ?>
        <h3>Ganhos Totais: R$ <?= number_format($ganhos_totais, 2, ',', '.') ?></h3>

<section class="data">
<h3>Filtrar por data</h3>
<form method="GET">
  <div class="inicio">
    <input type="hidden" name="id" value="<?= htmlspecialchars($barbeiro_id) ?>">
    <label for="inicio">Início:</label>
    <input type="date" name="inicio" id="inicio" value="<?= htmlspecialchars($data_inicio) ?>">
    
  </div>
  <div class="fim">
    <label for="fim">Fim:</label>
    <input type="date" name="fim" id="fim" value="<?= htmlspecialchars($data_fim) ?>">
    </div>
    <button type="submit">Filtrar</button>
</form>
</section>
</div>
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
                                <a href="editar_agendamento.php?id=<?= $agendamento['id'] ?>">Alterar</a> |
                                <a href="excluir_agendamento.php?id=<?= $agendamento['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este agendamento?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</b>
</html>
