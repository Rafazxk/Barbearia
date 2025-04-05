<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['barbeiro_id'])) {
    header("Location: login.html");
    exit;
}

$barbeiro_id = $_SESSION['barbeiro_id'];
$data = $_POST['data'];
$abrir = $_POST['abrir'];
$horario_abertura = $_POST['horario_abertura'] ?? null;
$horario_fechamento = $_POST['horario_fechamento'] ?? null;
$motivo = $_POST['motivo'] ?? null;
$fechado = ($abrir == "0") ? 1 : 0;

$stmt = $conn->prepare("INSERT OR REPLACE INTO configuracoes_barbeiro 
    (barbeiro_id, data, fechado, horario_abertura, horario_fechamento, motivo)
    VALUES (?, ?, ?, ?, ?, ?)");

$stmt->execute([$barbeiro_id, $data, $fechado, $horario_abertura, $horario_fechamento, $motivo]);

echo "<script>alert('Configuração salva com sucesso!'); window.location.href = 'configuracoes.php';</script>";
?>
