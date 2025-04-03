<?php
try {
    $conn = new PDO('sqlite:/data/data/com.termux/files/home/final'); // Substitua pelo caminho correto do seu banco
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  /* echo "Conexão bem-sucedida!"; */
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>
