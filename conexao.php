<?php
try {
    $conn = new PDO('sqlite:' . __DIR__ . '/database/final.db');

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   echo "Conexão bem-sucedida!"; 
} catch (PDOException $e) {
    /*echo "Erro de conexão: " . $e->getMessage();*/
}
?>
