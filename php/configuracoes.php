<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['barbeiro_id'])) {
    header("Location: login.html");
    exit;
}

$barbeiro_id = $_SESSION['barbeiro_id'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Configurações da Barbearia</title>
</head>
<body>
    <h2>Configurações da Barbearia</h2>

    <form action="salvar_config.php" method="post">
        <label>Data:</label>
        <input type="date" name="data" required><br>

        <label>Abrir nesse dia?</label>
        <select name="abrir">
            <option value="1">Sim</option>
            <option value="0">Não</option>
        </select><br>

        <label>Horário de abertura:</label>
        <input type="time" name="horario_abertura"><br>

        <label>Horário de fechamento:</label>
        <input type="time" name="horario_fechamento"><br>

        <label>Motivo (opcional):</label>
        <input type="text" name="motivo"><br>

        <input type="submit" value="Salvar Configuração">
    </form>

    <hr>

    <?php
    // Exibir datas que estão marcadas como fechadas
    $stmt = $conn->prepare("SELECT data, motivo FROM configuracoes_barbeiro WHERE barbeiro_id = ? AND fechado = 1 ORDER BY data ASC");
    $stmt->execute([$barbeiro_id]);
    $datas_fechadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($datas_fechadas) {
        echo "<h3>📅 Datas em que a barbearia estará fechada:</h3><ul>";
        foreach ($datas_fechadas as $row) {
            $motivo = $row['motivo'] ? " - Motivo: " . htmlspecialchars($row['motivo']) : "";
            echo "<li><strong>" . $row['data'] . "</strong>$motivo</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: green;'>Nenhuma data marcada como fechada até agora.</p>";
    }
    ?>
</body>
</html>
