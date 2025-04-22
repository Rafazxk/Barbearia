<?php
include "conexao.php";

// Verifica se um ID de agendamento foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID inválido!";
    exit;
}

$id = $_GET['id'];

// Verifica se o ID do barbeiro foi passado via GET (se for necessário no seu formulário)
$barbeiro_id = isset($_GET['barbeiro_id']) ? $_GET['barbeiro_id'] : null;

// Obtém a lista de serviços disponíveis
$stmt = $conn->query("SELECT id, nome, preco FROM servico");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtém os dados do agendamento
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

// Consulta os dados do cliente
$stmt_cliente = $conn->prepare("SELECT nome, telefone FROM cliente WHERE id = ?");
$stmt_cliente->execute([$cliente_id]);
$cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

// Consulta a lista de barbeiros disponíveis
$barbeiros = $conn->query("SELECT id, nome FROM barbeiro")->fetchAll(PDO::FETCH_ASSOC);

// Se o formulário for enviado, atualiza os dados do agendamento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'] ?? null;
    $telefone = $_POST['telefone'] ?? null;
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $servico_id = $_POST['servico_id'] ?? '';
    $barbeiro_id = $_POST['barbeiro_id'] ?? '';

    if (!$data || !$hora || !$servico_id || !$barbeiro_id) {
        echo "Todos os campos são obrigatórios!";
        exit;
    }

    // Atualiza o agendamento no banco de dados
    $stmt_update = $conn->prepare("UPDATE agendamento_novo SET data = ?, hora = ?, servico = ?, barbeiro_id = ?, cliente_nome = ?, telefone = ? WHERE id = ?");
    
    if ($stmt_update->execute([$data, $hora, $servico_id, $barbeiro_id, $nome, $telefone, $id])) {
        header("Location: concluido.php?id=$id");
        exit;
    } else {
        echo "Erro ao atualizar!";
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
        <input type="date" name="data" id="data" value="<?= $_GET['data'] ?? $agendamento['data'] ?>" onchange="this.form.submit()">
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
                $horarios_disponiveis = [];
                if (empty($horarios_disponiveis)):
                ?>
                    <option value="<?= $agendamento['hora'] ?>">Nenhum horário disponível</option>
                <?php else: ?>
                    <?php foreach ($horarios_disponiveis as $h): ?>
                        <option value="<?= $h ?>" <?= ($h == $agendamento['hora']) ? 'selected' : '' ?>><?= $h ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
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
