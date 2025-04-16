<?php
include "conexao.php";

// Verifica se um ID de agendamento foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID inválido!";
    exit;
}

$id = $_GET['id'];
$barbeiro_id = ['barbeiro_id'];


$stmt = $conn->query("SELECT id, nome, preco FROM servico");

$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtém os dados do agendamento
$stmt = $conn->prepare("SELECT a.id, c.nome AS cliente_nome, a.data, a.hora, s.nome AS servico, s.preco 
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

// Se o formulário for enviado, atualiza os dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_nome = $_POST['cliente_nome'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $servico_id = $_POST['servico_id'];
     
    // Atualiza o agendamento no banco
    $stmt = $conn->prepare("UPDATE agendamento_novo SET data = ?, hora = ?, servico = ? WHERE id = ?");
    if ($stmt->execute([$nome, $data, $hora, $servico_id, $id])) {
        echo "Agendamento atualizado com sucesso!";
        if (isset($agendamento['barbeiro_id'])) {
            echo "<br><a href='concluido.php?id=".$id."'>Voltar</a>";
            header("Location: concluido.php?id=$id");
exit;
        } else {
        echo "<br><a href='barbeiro.php'>Voltar</a>"; 
        }
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
</head>
<body>
   
<main class="principal">
   <h1>Escolha seu Agendamento</h1>
  <!-- escolha do barbeiro -->

  
    <form method="GET" action="">
    <div class="escolha_barbeiro">
     <div class="barbeiro">
        <label for="barbeiro">Escolha o barbeiro:</label>
         <select name="barbeiro_id" id="barbeiro" required onchange="this.form.submit()">
            <option value="">Selecione...</option>
              <?php foreach ($barbeiros as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $barbeiro_id == $b['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
     </div>

  <div class="data">
        <label for="data">Data:</label>
        <input type="date" name="data" id="data" value="<?=
        $agendamento['data'] ?>" onchange="this.form.submit()">
  </div>     
    </form>
  </div>

    <?php if ($barbeiro_id): ?>
       
        <form method="POST" action="concluido.php" id="form">
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
                <?php if (empty($horarios_disponiveis)): ?>
                    <option value="<?= $agendamento['hora'] ?>">Nenhum horário disponível</option>
                <?php else: ?>
                    <?php foreach ($horarios_disponiveis as $h): ?>
                        <option value="<?= $h ?>"><?= $h ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
 </div>
 <div class="informacoes_cliente">
            <label for="nome">Seu nome:</label>
            <input type="text" name="nome" id="nome" 
            value="<?= $agendamento['cliente_nome']?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" value="<?=
            $agendamento['telefone'] ?>" required>
             
            <button type="submit">Agendar</button>
        
</div>
        </form>

<?php endif; ?>

        <button type="submit">Salvar Alterações</button>
  
</body>
</html>
