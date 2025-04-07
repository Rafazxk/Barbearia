<?php
session_start();
include "conexao.php";

$barbeiro_id = $_SESSION['barbeiro_id'];
$data = $_POST['data'];
$abrir = $_POST['abrir'];
$horario_abertura = $_POST['horario_abertura'];
$horario_fechamento = $_POST['horario_fechamento'];
$motivo = $_POST['motivo'];

$sql = "INSERT INTO configuracoes_barbeiro (barbeiro_id, data, abrir, horario_abertura, horario_fechamento, motivo)
        VALUES (?, ?, ?, ?, ?, ?)
        ON CONFLICT(barbeiro_id, data) DO UPDATE SET 
            abrir=excluded.abrir, 
            horario_abertura=excluded.horario_abertura, 
            horario_fechamento=excluded.horario_fechamento, 
            motivo=excluded.motivo";

$stmt = $conn->prepare($sql);
$stmt->execute([$barbeiro_id, $data, $abrir, $horario_abertura, $horario_fechamento, $motivo]);

echo "Configuração salva com sucesso!";
