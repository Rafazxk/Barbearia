<?php
try {
    // Conectar ao banco SQLite
    $pdo = new PDO("sqlite:barbearia.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $agendamentoRealizado = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obter dados do formulário
        $barbeiro = $_POST['barbeiro'];
        $dia = $_POST['dia'];
        $horario = $_POST['horario'];
        $servico = $_POST['servico'];
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];

        // Inserir dados na tabela cliente
        $sql = "INSERT INTO cliente (nome, telefone, barbeiro, dia, horario, servico) 
                VALUES (:nome, :telefone, :barbeiro, :dia, :horario, :servico)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':barbeiro', $barbeiro);
        $stmt->bindParam(':dia', $dia);
        $stmt->bindParam(':horario', $horario);
        $stmt->bindParam(':servico', $servico);

        if ($stmt->execute()) {
            $agendamentoRealizado = true;
            echo "<script>alert('Agendamento realizado com sucesso!');</script>";
        }
    }
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento Concluído</title>
    <link rel="stylesheet" href="styles/contato.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
   <header class="hero">
       <a href="index.html" id="voltar">voltar</a>
       <img src="imagens/tk_logo.png" id="logo_name">
        <nav>
            <a href="index.html" id="btn_home">Home</a>
            <a href="index.html" id="btn_servicos">Serviços</a>            
            <a href="index.html" id="btn_contato">Contato</a>
        </nav>       
    </header>

    <main class="main">
        <div class="tela_dados">
            <h2 id="concluido">Agendamento Concluído com sucesso!</h2>

            <?php if ($agendamentoRealizado): ?>
                <div class="dados">
                    <div class="barbeiro"><p>Barbeiro:</p> <p><?= htmlspecialchars($barbeiro) ?></p></div>
                    <div class="dia"><p>Data:</p> <p><?= htmlspecialchars($dia) ?></p></div>
                    <div class="horario"><p>Horário:</p> <p><?= htmlspecialchars($horario) ?></p></div>
                    <div class="servico"><p>Serviço:</p> <p><?= htmlspecialchars($servico) ?></p></div>
                </div>

                <div class="alterar">
                    <button type="submit" onclick="">Alterar Agendamento</button>
                    <button type="submit" id="botaoExcluir()">Excluir Agendamento</button>
                </div>
            <?php else: ?>
                <p>Erro ao processar o agendamento.</p>
            <?php endif; ?>
        </div>

        <div class="contatos">  
            <div id="contato_wpp">
                <a href=""><img src="https://img.icons8.com/m_outlined/512/FFFFFF/whatsapp.png" alt="WhatsApp"></a>
                <p>WhatsApp</p>
            </div>

            <div id="contato_ig">
                <a href=""><img src="https://img.icons8.com/win10/512/FFFFFF/instagram-new.png" alt="Instagram"></a>
                <p>Instagram</p>
            </div>

            <div id="compartilhar">
                <img src="https://img.icons8.com/m_sharp/512/FFFFFF/share.png" alt="Compartilhar">
                <p>Compartilhar</p>
            </div>
        </div>
    </main>
  
    <script src="scripts/contato.js"></script>
</body>
</html>
