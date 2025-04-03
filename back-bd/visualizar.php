<?php
include "conexao.php"; // Inclua a conexão com o banco de dados

// Consultar os agendamentos e os clientes relacionados
$stmt = $conn->prepare("
    SELECT agendamento_novo.id, cliente.nome AS cliente_nome, cliente.telefone, agendamento_novo.data, agendamento_novo.hora, servico.nome AS servico_nome, barbeiro_id
    FROM agendamento_novo
    JOIN cliente ON agendamento_novo.cliente_id = cliente.id
    JOIN servico ON agendamento_novo.servico = servico.id
    ORDER BY agendamento_novo.data, agendamento_novo.hora
");
$stmt->execute();
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Agendamentos</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Agendamentos</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Serviço</th>
                <th>Barbeiro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agendamentos as $agendamento): ?>
                <tr>
                    <td><?= $agendamento['id']; ?></td>
                    <td><?= $agendamento['cliente_nome']; ?></td>
                    <td><?= $agendamento['telefone']; ?></td>
                    <td><?= $agendamento['data']; ?></td>
                    <td><?= $agendamento['hora']; ?></td>
                    <td><?= $agendamento['servico_nome']; ?></td>
                    <td><?= $agendamento['barbeiro_id']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
