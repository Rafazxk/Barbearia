<?php
include_once "conexao.php";
session_start();

// Unificar a obtenção do barbeiro_id e da data
$barbeiro_id = $_SESSION['barbeiro_id'] ?? $_GET['barbeiro_id'] ?? $_POST['barbeiro_id'] ?? null;
$data_do_agendamento = $_POST['data'] ?? $_GET['data'] ?? date("Y-m-d");

if (!$barbeiro_id) {
    echo "Barbeiro não especificado.";
    exit;
}

// Verificar se a barbearia está configurada como fechada
$stmt = $conn->prepare("SELECT * FROM configuracoes_barbeiro 
                        WHERE barbeiro_id = ? AND data = ?");
$stmt->execute([$barbeiro_id, $data_do_agendamento]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

if ($config && strtolower($config['motivo']) === 'fechado') {
    echo "<p style='color:red; font-weight:bold;'>⚠ A barbearia está fechada nesta data ({$data_do_agendamento}). Por favor, escolha outro dia.</p>";
    exit;
}

// Definir horários
$horarios_disponiveis = [
    "08:00", "09:00", "10:00", "11:00", "12:00",
    "13:00", "14:00", "15:00", "16:00", "17:00",
    "18:00", "19:00", "20:00"
];

// Buscar serviços
$stmt = $conn->prepare("SELECT id, nome, preco FROM servico");
$stmt->execute();
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar horários ocupados
$horarios_ocupados = [];
$stmt = $conn->prepare("SELECT hora FROM agendamento_novo WHERE data = ? AND barbeiro_id = ?");
$stmt->execute([$data_do_agendamento, $barbeiro_id]);
$horarios_ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Filtrar horários disponíveis
$horarios_disponiveis = array_values(array_diff($horarios_disponiveis, $horarios_ocupados));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento</title>
    <link rel="stylesheet" href="../styles/agendamento.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
  
    
<body>  
<header class="hero">
       <a href="../index.html" id="voltar">voltar</a>
       <img src="../imagens/tk_logo.png" id="logo_name">
        <nav>
            <a href="index.html" id="btn_home">Home</a>
            <a href="index.html" id="btn_servicos">Serviços</a>            
            <a href="index.html" id="btn_contato">Contato</a>
        </nav>       
    </header>

  <main class="main">
    <h2>Agendamento de Serviço</h2>
<div class="escolha-barbeiro">
    <form action="" method="GET">
        <label for="barbeiro">Escolha o Barbeiro:</label>
        <select name="barbeiro_id" id="barbeiro" required onchange="this.form
         .submit()">
            <option value="">Selecione um barbeiro</option>
            <option value="1" <?= $barbeiro_id == 1 ? 'selected' : '' ?>>Tharsys</option>
            <option value="2" <?= $barbeiro_id == 2 ? 'selected' : '' ?>>Kleyton</option>
            <option value="3" <?= $barbeiro_id == 3 ? 'selected' : '' ?>>Gustavo</option>
        </select>
    </form>
</div>

<?php if ($barbeiro_id): ?>
     
   <div class="agendamento">
        
        <form action="concluido.php" method="POST">
            <input type="hidden" name="barbeiro_id" value="<?= $barbeiro_id ?>">

            <br>
      <div class="data">
      
      <?php
$barbearia_fechada = false;
if ($config && strtolower($config['motivo']) == 'fechado') {
    $barbearia_fechada = true;
}
?>
<?php if ($barbearia_fechada): ?>
    <p style="color: red; font-weight: bold; margin-bottom: 1em;">
        ⚠ A barbearia estará fechada nesta data (<?= htmlspecialchars($data_do_agendamento) ?>). Por favor, escolha outra data.
    </p>
<?php endif; ?>

   

<label for="data">Escolha a Data:</label>
            <input type="date" name="data" id="data" value="<?= htmlspecialchars($data_do_agendamento ) ?>" required onchange="updateURL()">
      </div>
            <br><br>
      <div class="hora">
            <label for="hora">Escolha o Horário:</label>
            <select name="hora" id="hora" required>
                <?php if (empty($horarios_disponiveis)): ?>
                    <option value="">Nenhum horário disponível</option>
                <?php else: ?>
                    <?php foreach ($horarios_disponiveis as $hora): ?>
                        <option value="<?= $hora ?>"><?= $hora ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
      </div>
            <br><br>
      <div class="servicos">
            <label for="servico">Escolha o Serviço:</label>
            <select name="servico" id="servico" required>
                <?php foreach ($servicos as $servico): ?>
                    <option value="<?= $servico['id']; ?>">
                        <?= $servico['nome']; ?> - R$ <?= number_format($servico['preco'], 2, ',', '.'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
   </div> 
            <br><br>
    <div class="dados-cliente">
        <h2>Insira seus Dados</h2>
      
      <div class="dados">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" placeholder ="Insira seu nome" required>
            <br><br>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" id="telefone" placeholder ="Insira seu telefone" required oninput="formatarTelefone(this)" required>
            <br><br>
       </div>
            <button type="submit">Agendar</button>
         </form>
       </div>
       
    <?php endif; ?>
    
  </main>
  
    <script>
        function updateURL() {
            const data = document.getElementById('data').value;
            const url = new URL(window.location.href);
            url.searchParams.set('data', data);
            window.location.href = url.toString();
        }
function formatarTelefone(input) {
    let telefone = input.value.replace(/\D/g, '');
    

    if (telefone.length > 11) telefone = telefone.slice(0, 11); // Limita ate 11 dígitos

    if (telefone.length > 10) {
        input.value = `(${telefone.slice(0, 2)}) ${telefone.slice(2, 7)}-${telefone.slice(7)}`;
    } else if (telefone.length > 6) {
        input.value = `(${telefone.slice(0, 2)}) ${telefone.slice(2, 6)}-${telefone.slice(6)}`;
    } else if (telefone.length > 2) {
        input.value = `(${telefone.slice(0, 2)}) ${telefone.slice(2)}`;
    } else if (telefone.length > 0) {
        input.value = `(${telefone}`;
    }
}
    </script>
</body>
</html>
