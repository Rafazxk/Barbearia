<?php
try {
    $pdo = new PDO("sqlite:barbearia.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM cliente";
    $stmt = $pdo->query($sql);

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "Nome: " . $row['nome'] . " - Serviço: " . $row['servico'] . "<br>";
        }
    } else {
        echo "Nenhum cliente encontrado!";
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
}
?>
<?php
echo "Caminho do Banco: " . realpath("barbearia.db");
?>