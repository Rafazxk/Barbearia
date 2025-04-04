<?php
session_start();
include "conexao.php";

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

$tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table';")->fetchAll();
foreach ($tables as $table) {
    echo "Tabela: " . $table['name'] . "<br>";
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$stmt->execute([$usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['senha'] === $senha) {
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['barbeiro_id'] = $user['barbeiro_id'];
    header("Location: barbeiro.php");
    exit;
} else {
    echo "Usuário ou senha inválidos.";
}
