<?php
try {
    // Conectar ao banco SQLite
    $pdo = new PDO("sqlite:barbearia.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obter o total de cadastros
    $query = "SELECT COUNT(*) AS total FROM cliente";
    $stmt = $pdo->query($query);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Consulta para listar todos os clientes
    $query = "SELECT * FROM cliente";
    $stmt = $pdo->query($query);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Agendamentos</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Total de agendamentos: <?= $total ?></h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Telefone</th>
            <th>Barbeiro</th>
            <th>Data</th>
            <th>Horário</th>
            <th>Serviço</th>
        </tr>
        <?php foreach ($clientes as $cliente): ?>
        <tr>
            <td><?= htmlspecialchars($cliente['id']) ?></td>
            <td><?= htmlspecialchars($cliente['nome']) ?></td>
            <td><?= htmlspecialchars($cliente['telefone']) ?></td>
            <td><?= htmlspecialchars($cliente['barbeiro']) ?></td>
            <td><?= htmlspecialchars($cliente['dia']) ?></td>
            <td><?= htmlspecialchars($cliente['horario']) ?></td>
            <td><?= htmlspecialchars($cliente['servico']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
