<?php
include "conexao.php";
session_start();

if (!isset($_SESSION['barbeiro_id'])) {
    header("Location: login.html");
    exit;
}

$barbeiro_id = $_SESSION['barbeiro_id'];

if (isset($_POST['data']) && isset($_POST['abrir'])) {
    $data = $_POST['data'];
    $abrir = $_POST['abrir'];
    $horario_abertura = $_POST['horario_abertura'] ?? null;
    $horario_fechamento = $_POST['horario_fechamento'] ?? null;
    $motivo = $_POST['motivo'] ?? null;

    // INSERE OU ATUALIZA se já existir mesma data + barbeiro_id
    $sql = "
        INSERT INTO configuracoes_barbeiro 
        (barbeiro_id, data, abrir, horario_abertura, horario_fechamento, motivo)
        VALUES (:barbeiro_id, :data, :abrir, :horario_abertura, :horario_fechamento, :motivo)
        ON CONFLICT(barbeiro_id, data) DO UPDATE SET
            abrir = excluded.abrir,
            horario_abertura = excluded.horario_abertura,
            horario_fechamento = excluded.horario_fechamento,
            motivo = excluded.motivo
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':barbeiro_id' => $barbeiro_id,
        ':data' => $data,
        ':abrir' => $abrir,
        ':horario_abertura' => $horario_abertura,
        ':horario_fechamento' => $horario_fechamento,
        ':motivo' => $motivo
    ]);

    echo "Configuração salva com sucesso!";
} else {
    echo "Campos obrigatórios não foram preenchidos.";
}
?>
