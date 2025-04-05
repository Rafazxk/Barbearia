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
</body>
</html>
