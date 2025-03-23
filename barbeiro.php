<?php
// Conectar com o SQLite
try {
    $pdo = new PDO("sqlite:barbearia.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obter os clientes
    $query = "SELECT * FROM cliente LIMIT 6"; // Pega no máximo 6 clientes para preencher as divs
    $stmt = $pdo->query($query);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia TK</title>
    <link rel="stylesheet" href="styles/barbeiro.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <header class="head">
        <h1>Barbearia TK</h1>
        <nav>
            <select class="perfil">
                <option value="">Barbeiro</option>
                <option value="tharsys">Tharsys</option>
                <option value="gustavo">Gustavo</option>
                <option value="kleyton">Kleyton</option>
            </select>
            <p>ativa</p>
        </nav>
    </header>

    <main class="main">
        <div class="corpo">
            <h2>Próximos horários</h2>

            <div class="quadro_horarios">
                <?php
                // Criar as divs q1, q2... e preencher com os clientes
                for ($i = 0; $i < 6; $i++) {
                    echo '<div id="q'.($i+1).'">';
                    if (isset($clientes[$i])) {
                        echo "<p>Cliente: <strong>".$clientes[$i]['nome']."</strong></p>";
                        echo "<p>data: ".$clientes[$i]['dia']."</p>";
                        echo "<p>Horário: ".$clientes[$i]['horario']."</p>";
                        echo "<p>telefone: ".$clientes[$i]['telefone']."</p>";
                     
                    } else {
                        echo "<p>Vago</p>";
                    }
                    echo '</div>';
                }
                ?>
            </div>

            <select class="filtrar">
                <option value="">Filtrar Clientes</option>
            </select>
        </div>
    </main>

</body>
</html>
