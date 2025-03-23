<?php
try {
    $pdo = new PDO("sqlite:barbearia.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Deletar todos os registros da tabela cliente
    $sql = "DELETE FROM cliente";
    $pdo->exec($sql);

    echo "Todos os registros foram removidos com sucesso!";
} catch (PDOException $e) {
    die("Erro ao apagar os registros: " . $e->getMessage());
}
?>