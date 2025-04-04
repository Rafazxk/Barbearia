<?php
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $telefone = $_POST["telefone"];
    $barbeiro_id = $_POST["barbeiro_id"];
    $data = $_POST["data"];
    $hora = $_POST["hora"];
    $servico = $_POST["servico"];

    // cliente ja existe?
    //veifica pelo telefone
    
    $stmt = $conn->prepare("SELECT id FROM cliente WHERE telefone = ?");
    $stmt->execute([$telefone]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        // Insere o novo cliente
        $stmt = $conn->prepare("INSERT INTO cliente (nome, telefone) VALUES (?, ?)");
        $stmt->execute([$nome, $telefone]);
        $cliente_id = $conn->lastInsertId();
    } else {
        $cliente_id = $cliente['id'];
    }

    // Inserir o agendamento na tabela agendamento_novo
    $stmt = $conn->prepare("INSERT INTO agendamento_novo (barbeiro_id, cliente_id, data, hora, servico) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$barbeiro_id, $cliente_id, $data, $hora, $servico])) {
        echo "Agendamento realizado com sucesso!";
    } else {
        echo "Erro ao agendar.";
    }
}
?>

 <?php /*
 if($barbeiro_id == 1){
  return "Tharsys";  
 }else if($barbeiro_id == 2){
   return "Kleyton";
 }
 else{
   return "Gustavo";
 }
*/
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento</title>
    <link rel="stylesheet" href="../styles/concluido.css">
</head>
<body>
  <header>
    <header class="hero">
       <a href="index.html" id="voltar">voltar</a>
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
        
        <div class="data&hora">
       
          <p>data:  <?php echo $data; ?></p>
           
           <div>
     
          </div>
        </div>
         <div class="servico">
          <?php 
          
          //imprime o corte
          echo "<p>Serviço: " . 
          $servico . "</p>";
          
          ?>
         </div>
         
        
         
         <div class="barbeiro-id">
           <?php 
           echo "<p>Barbeiro: " .
           $barbeiro_id . "</p>";
           
           ?>
         </div>
    <div class="btn-alterar">
      <div class="alterar">
      
      <a href="edit.php?id=<?= $row['id'] ?>">Alterar</a>
      
      
      
      </div>
    <div class="excluir">
    <a href="excluir.php?id=<?=$barbeiro_id['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
   </div>
    </div>
        </div>
  
  
 

  </main>
  
  
  
</body>
</html>




