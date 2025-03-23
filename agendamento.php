
<?php
try {
    // Conectar ao banco SQLite
    $pdo = new PDO("sqlite:barbearia.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obter os dados do formulário
        $barbeiro = $_POST['barbeiro'];
        $dia = $_POST['dia'];
        $horario = $_POST['horario'];
        $servico = $_POST['servico'];
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];

        // Inserir os dados na tabela cliente
        $sql = "INSERT INTO cliente (nome, telefone, barbeiro, dia, horario, servico) VALUES (:nome, :telefone, :barbeiro, :dia, :horario, :servico)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':barbeiro', $barbeiro);
        $stmt->bindParam(':dia', $dia);
        $stmt->bindParam(':horario', $horario);
        $stmt->bindParam(':servico', $servico);
        $stmt->execute();

        echo "<script>alert('Agendamento realizado com sucesso!');</script>";
    }
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>

<?php

function celular($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone); // Remove tudo que não for número
    return preg_match('/^\d{11}$/', $telefone); // Valida 11 dígitos
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TK Barbearia - Agendamento</title>
    <link rel="stylesheet" href="styles/agendamento.css">
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="hero">
       <a href="index.html" id="voltar">voltar</a>

       <img src="imagens/tk_logo.png" id="img_logo">
        <nav>
            <a href="index.html" id="btn_home">Home</a>
            <a href="index.html" id="btn_servicos">Serviços</a>            
            <a href="index.html" id="btn_contato">contato</a>
        </nav>       
    </header>

    <main>
        <!-- FORMULÁRIO COMEÇA AQUI -->
        
        <form action="contato.php" method="POST">
            <section class="barbeiro">
                <h2>Barbeiro</h2>
                <select name="barbeiro">
                    <option value="">Escolha um barbeiro</option>
                    <option value="Tharsys">Tharsys</option>
                    <option value="Kleiton">Kleiton</option>
                    <option value="Gustavo">Gustavo</option>
                </select>
            </section>

            <section class="agendamento">
        <h2>Data e horário</h2>
         
        <input type="date" id="date" name="dia">
                
       </div>

                <select name="horario">
                    <option value="">Escolha um horário</option>
                    <option value="09:00">09:00</option>
                    <option value="10:00">10:00</option>
                    <option value="11:00">11:00</option>
                    <option value="12:00">12:00</option>
                    <option value="14:00">14:00</option>
                    <option value="15:00">15:00</option>
                    <option value="16:00">16:00</option>
                    <option value="17:00">17:00</option>
                    <option value="18:00">18:00</option>
                    <option value="19:00">19:00</option>
                    <option value="20:00">20:00</option>
                </select>
            </section>

            <section class="servicos">
                <h2>Escolha um serviço</h2>
                
            
        <select name="servico">
        <option value="">Escolha seu serviço</option>
   <option value="degrade">Corte Simples (1 pente) R$ 15,00</option>
   <option value="militar">Corte Militar (2 pentes) R$ 20,00</option>
        <option value="Social">Corte Degradê R$ 25,00 </option>
        <option value="maquina-e-tesoura">Máquina e Tesoura R$ 25,00</option>
        <option value="tesoura">Só Tesoura R$ 25,00</option>
        <option value="barba">Barba R$ 20,00</option>
        <option value="canto">Cantinho R$ 5,00</option>


        <option value="" disabled>COMBOS</option>
        <option value="Corte + Barba          R$ 35,00">Corte + Barba R$ 35,00</option>
        <option value="c&b&l">Corte + Barba + Limpeza de Pele R$ 50,00</option>
        <option value="barbaecorte">Corte + Luzes(A partir) R$ 60,00</option>
        <option value="corte&luzes">Corte + Luzes(Moicano, A parir) R$ 65,00</option>
        <option value="corte&pigmento">Corte + Pigmentação(A partir) R$ 35,00</option>
        <option value="barbaecorte">Corte + Platinado(A partir) R$ 75,00</option>
        <option value="barbaecorte">Corte + Platinado(Moicano, A partir) R$ 80,00</option>
        <option value="barbaecorte">Corte + Alisante(A partir) R$ 50,00</option>

        </select>
            </section>
       
            <section id="info">
            <h2>Deixe suas Informações aqui!</h2>
            
              <label for="name">Nome:</label>
              <input type="text" name="nome" placeholder="Insira seu nome">
     
              <label for="tel">Telefone:</label> 
              <input type="text" name="telefone" placeholder="Insira seu Telefone">
              
            </section>
             <button type="submit" id="confirmar">Confirmar Agendamento</button>
        </form>
    </main>

    <script>
       
        document.getElementById("confirmar").addEventListener("click", (event) => {
            let horario = document.querySelector("select[name='horario']").value;
            if (horario === "") {
                alert("Por favor, selecione um horário.");
                event.preventDefault(); 
            }
        });
    </script>
</body>
</html>