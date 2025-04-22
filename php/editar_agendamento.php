<?php
include "conexao.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
   echo "ID inválido!";
   exit;
}

$id = $_GET['id'];
$barbeiro_id = isset($_GET['barbeiro_id']) ? $_GET['barbeiro_id'] : null;

// Definir data selecionada corretamente
$data_selecionada = $_GET['data'] ?? null;

$stmt = $conn->query("SELECT id, nome, preco FROM servico");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT a.id, a.cliente_id, c.nome AS cliente_nome, c.telefone, a.data, a.hora, a.servico, s.nome AS servico_nome, s.preco, a.barbeiro_id
FROM agendamento_novo a
JOIN cliente c ON a.cliente_id = c.id
JOIN servico s ON a.servico = s.id
WHERE a.id = ?");
$stmt->execute([$id]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
   echo "Agendamento não encontrado!";
   exit;
}

$cliente_id = $agendamento['cliente_id'];
$stmt_cliente = $conn->prepare("SELECT nome, telefone FROM cliente WHERE id = ?");
$stmt_cliente->execute([$cliente_id]);
$cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

$barbeiros = $conn->query("SELECT id, nome FROM barbeiro")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $nome = $_POST['nome'] ?? null;
   $telefone = $_POST['telefone'] ?? null;
   $data = $_POST['data'] ?? null;
   $hora = $_POST['hora'] ?? '';
   $servico_id = $_POST['servico_id'] ?? '';
   $barbeiro_id = $_POST['barbeiro_id'] ?? '';

   if (!$data || !$hora || !$servico_id || !$barbeiro_id) {
       echo "Todos os campos são obrigatórios!";
       exit;
   }

   $stmt_update = $conn->prepare("UPDATE agendamento_novo SET data = ?, hora = ?, servico = ?, barbeiro_id = ? WHERE id = ?");
   if ($stmt_update->execute([$data, $hora, $servico_id, $barbeiro_id, $id])) {
       if ($nome !== $cliente['nome'] || $telefone !== $cliente['telefone']) {
           $stmt_cliente_update = $conn->prepare("UPDATE cliente SET nome = ?, telefone = ? WHERE id = ?");
           $stmt_cliente_update->execute([$nome, $telefone, $cliente_id]);
       }
       header("Location: concluido.php?id=$id");
       exit;
   } else {
       echo "Erro ao atualizar!";
   }
}

// Definir data selecionada (caso GET esteja vazio, usa do agendamento)
$data_selecionada = $data_selecionada ?? $agendamento['data'];

// Buscar horários agendados
$horarios_disponiveis = [];
if ($barbeiro_id && $data_selecionada) {
    $stmt = $conn->prepare("SELECT hora FROM agendamento_novo WHERE barbeiro_id = ? AND data = ?");
    $stmt->execute([$barbeiro_id, $data_selecionada]);
    $horarios_agendados = $stmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    $horarios_agendados = [];
}

$horarios_possiveis = [
    "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
    "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30",
    "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00"
];

// Gerar horários disponíveis
foreach ($horarios_possiveis as $hora) {
    if (!in_array($hora, $horarios_agendados) || $hora == $agendamento['hora']) {
        $horarios_disponiveis[] = $hora;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="../styles/edit.css">   
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
    <a href="../index.html"><img src="../imagens/seta.png"
    class="voltar"></a>
  <img src="../imagens/tk_logo.png" id="logo_name">
  
  </header>

<main class="principal">
   <h1>Altere seu agendamento</h1>
 
  <!-- escolha do barbeiro -->
  
    <form method="GET" action="">
       <input type="hidden" name="id" value="<?= $id ?>">
    <div class="escolha_barbeiro">
     <div class="barbeiro">
        <label for="barbeiro">Escolha o barbeiro:</label>
         <select name="barbeiro_id" id="barbeiro" required onchange="this.form.submit()">
            <option value="<?= $barbeiro_id ?>">Selecione...</option>
              <?php foreach ($barbeiros as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $barbeiro_id == $b['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
     </div>

  <div class="data">
        <label for="data">Data:</label>
        <input type="date" name="data" value="<?= $data_selecionada ?>">
  </div>     
    </form>
  </div>

    <?php if ($barbeiro_id): ?>
       
        <form method="POST" action="" id="form">
           <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">
            <input type="hidden" name="barbeiro_id" value="<?= $barbeiro_id ?>">
            <input type="hidden" name="data" value="<?= $data_selecionada ?>">
 <div class="servico">
            <label for="servico">Serviço:</label>
            <select name="servico_id" id="servico" required>
                <?php foreach ($servicos as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['nome']) ?> - R$ <?= number_format($s['preco'], 2, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
 </div>
 <div class="hora_agendamento">
 <label for="hora">Horário:</label>
    <select name="hora" id="hora" required>
        <?php
        if (empty($horarios_disponiveis)) {
            // Se não houver horários disponíveis
            echo '<option value="">Nenhum horário disponível</option>';
        } else {
            // Preenche o select com os horários disponíveis
            foreach ($horarios_disponiveis as $h) {
                echo '<option value="' . $h . '" ' . ($h == $agendamento['hora'] ? 'selected' : '') . '>' . $h . '</option>';
            }
        }
        ?>
    </select>
</div>

 <div class="informacoes_cliente">
            <label for="nome">Seu nome:</label>
            <input type="text" name="nome" id="nome" 
            value="<?= $agendamento['cliente_nome']?>" required>

 <label for="telefone">Telefone:</label>
 
<input type="text" name="telefone" id="telefone" value="<?= $agendamento['telefone'] ?>">

</div>

  <button type="submit">Salvar Alterações</button>
        </form>

<?php endif; ?>

      
  
</body>
</html>
