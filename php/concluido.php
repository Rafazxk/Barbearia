<?php

include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['id'])) {
    // Lógica de novo agendamento
    $nome = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $barbeiro_id = $_POST["barbeiro_id"];
    $data = $_POST["data"];
    $hora = $_POST["hora"];
    $servico = $_POST["servico"];
 

  // Agendamento vindo do form
$data_agendada = $_POST['data'];
$hora_agendada = $_POST['hora']; // formato HH:MM

// Verifica configuração para o dia
$stmt = $conn->prepare("SELECT fechado, horario_abertura, horario_fechamento FROM configuracoes_barbeiro WHERE barbeiro_id = ? AND data = ?");
$stmt->execute([$barbeiro_id, $data_agendada]);
$config = $stmt->fetch();

if ($config && $config['fechado']) {
    echo "<script>alert('A barbearia estará fechada nesta data!'); window.history.back();</script>";
    exit;
}

if ($config) {
    if (!empty($config['horario_abertura']) && !empty($config['horario_fechamento'])) {
        if ($hora_agendada < $config['horario_abertura'] || $hora_agendada > $config['horario_fechamento']) {
            echo "<script>alert('Horário fora do funcionamento configurado.'); window.history.back();</script>";
            exit;
        }
    }
}

 


    // Verifica cliente
    $stmt = $conn->prepare("SELECT id FROM cliente WHERE telefone = ?");
    $stmt->execute([$telefone]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        $stmt = $conn->prepare("INSERT INTO cliente (nome, telefone) VALUES (?, ?)");
        $stmt->execute([$nome, $telefone]);
        $cliente_id = $conn->lastInsertId();
    } else {
        $cliente_id = $cliente['id'];
    }

    // Insere agendamento
    $stmt = $conn->prepare("INSERT INTO agendamento_novo (barbeiro_id, cliente_id, data, hora, servico) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$barbeiro_id, $cliente_id, $data, $hora, $servico]);

    // Redireciona para si mesmo com o ID
    $agendamento_id = $conn->lastInsertId();
    header("Location: concluido.php?id=$agendamento_id");
    exit;
}

// Exibe os dados do agendamento (se ID estiver definido)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT a.id, a.data, a.hora, s.nome AS servico, b.nome AS barbeiro
                            FROM agendamento_novo a
                            JOIN servico s ON a.servico = s.id
                            JOIN barbeiro b ON a.barbeiro_id = b.id
                            WHERE a.id = ?");
    $stmt->execute([$id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($agendamento) {
        $agendamento_id = $agendamento['id'];
        $data = $agendamento['data'];
        $hora = $agendamento['hora'];
        $servico = $agendamento['servico'];
        $barbeiro = $agendamento['barbeiro'];
    } else {
        echo "Agendamento não encontrado!";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendamento</title>
  <link rel="stylesheet" href="../styles/concluido.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
<script>
  if (Notification.permission === "granted") {
    new Notification("Novo agendamento recebido!", {
      body: "Um cliente acabou de agendar um horário.",
      icon: "../imagens/tk_logo.png" // opcional
    });
  }
</script>
  <header class="hero">
    <a href="agendamento.php" id="voltar">< Voltar</a>
    <img src="../imagens/tk_logo.png" id="logo_name">
    <nav>
      <a href="index.html" id="btn_home">Home</a>
      <a href="index.html" id="btn_servicos">Serviços</a>
      <a href="index.html" id="btn_contato">Contato</a>
    </nav>
  </header>

  <main class="main">
    <div class="tela_dados">
      <h2 id="concluido">Agendamento Concluído com sucesso!</h2>

      <p>Data: <?= htmlspecialchars($data) ?></p>
      <p>Hora: <?= htmlspecialchars($hora) ?></p>
      <p>Serviço: <?= htmlspecialchars($servico) ?></p>
      <p>Barbeiro: <?= htmlspecialchars($barbeiro) ?></p>

      <div class="acoes">
        <a href="editar_agendamento.php?id=<?= $agendamento_id ?>">
          <button>Alterar</button>
        </a>
        <a href="excluir_agendamento.php?id=<?= $agendamento_id ?>" onclick="return confirm('Deseja realmente excluir este agendamento?');">
          <button>Excluir</button>
        </a>
      </div>
    </div>
  </main>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    if ("Notification" in window) {
      if (Notification.permission !== "granted") {
        Notification.requestPermission();
      }
    }
  });
</script>


</body>
</html>
