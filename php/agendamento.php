<?php
include_once "conexao.php";
include_once "funcoes.php";

// Buscar barbeiros
$stmt = $conn->query("SELECT id, nome FROM barbeiro");
$barbeiros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar serviços
$stmt = $conn->query("SELECT id, nome, preco FROM servico");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pega parâmetros
$barbeiro_id = $_GET["barbeiro_id"] ?? null;
$data_selecionada = $_GET["data"] ?? date("Y-m-d");

// Verificar se o barbeiro estará indisponível na data escolhida
$verifica = $conn->prepare("SELECT * FROM configuracoes_barbeiro 
                            WHERE barbeiro_id = ? AND data = ? 
                            AND (fechado = 1 OR abrir = 0)");
$verifica->execute([$barbeiro_id, $data_selecionada]);


$config = $verifica->fetch();

if ($config) {
    echo "<p style='color:red;'>Esse barbeiro estará indisponível nessa data. Escolha outra.</p>";
    exit;
}




// Define horários com base nas configurações do barbeiro
$horarios_disponiveis = [];
if ($barbeiro_id) {
    $horarios_disponiveis = horariosConfigurados($conn, $barbeiro_id, $data_selecionada);

    // Remove horários já agendados
    $stmt = $conn->prepare("SELECT hora FROM agendamento_novo WHERE data = ? AND barbeiro_id = ?");
    $stmt->execute([$data_selecionada, $barbeiro_id]);
    $horarios_ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $horarios_disponiveis = array_values(array_diff($horarios_disponiveis, $horarios_ocupados));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agendamento</title>
    <link rel="stylesheet" href="../styles/agendamento.css">
</head>
<body>
  <header>
    <h1>Agende seu horário</h1>
    <nav>
      <a href="#">Inicio</a>
    </nav>  
</header>



    <form method="GET" action="">
        <label for="barbeiro">Escolha o barbeiro:</label>
        <select name="barbeiro_id" id="barbeiro" required onchange="this.form.submit()">
            <option value="">Selecione...</option>
            <?php foreach ($barbeiros as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $barbeiro_id == $b['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="data">Data:</label>
        <input type="date" name="data" id="data" value="<?= $data_selecionada ?>" onchange="this.form.submit()">
    </form>

    <?php if ($barbeiro_id): ?>
        <form method="POST" action="concluido.php">
            <input type="hidden" name="barbeiro_id" value="<?= $barbeiro_id ?>">
            <input type="hidden" name="data" value="<?= $data_selecionada ?>">

            <label for="servico">Serviço:</label>
            <select name="servico_id" id="servico" required>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['nome']) ?> - R$ <?= number_format($s['preco'], 2, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="hora">Horário:</label>
            <select name="hora" id="hora" required>
                <?php if (empty($horarios_disponiveis)): ?>
                    <option value="">Nenhum horário disponível</option>
                <?php else: ?>
                    <?php foreach ($horarios_disponiveis as $h): ?>
                        <option value="<?= $h ?>"><?= $h ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <label for="nome">Seu nome:</label>
            <input type="text" name="nome" id="nome" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" required>
             
            <button type="submit">Agendar</button>
        </form>
    <?php endif; ?>
</body>
</html>
