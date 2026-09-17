<?php
    // Inicia a sessão PHP
    session_start();

    // Importa o arquivo de conexão com o banco de dados
    require 'conexao.php';

    // Inicializa a variável de mensagem de erro vazia
    $erro = '';

    // Verifica se o formulário foi enviado via método POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Remove espaços em branco extras do início e fim
        $email = trim($_POST['email']);
        $senha = trim($_POST['senha']);

        // Prepara a busca do usuário com o email e senha informados
        $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE email = ? AND senha = ?");

        // Executa a consulta passando os valores do email e senha
        $stmt->execute([$email, $senha]);

        // Busca os dados do usuário retornado e organiza em um array
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se encontrar um usuário, login válido
        if ($user) {
            // Salva o ID do usuário na sessão
            $_SESSION['id_usuario'] = $user['id_usuario'];

            // Salva o nome do usuário na sessão
            $_SESSION['nome'] = $user['nome'];

            // Redireciona para a página principal do sistema
            header("Location: principal.php");

            // Encerra para evitar que o código continue sendo executado
            exit;
        } else {
            // Caso estejam erradas, define a mensagem de erro
            $erro = "Falha na autenticação: E-mail ou senha inválidos. Tente novamente.";
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>SAEP - Login</title>
    <style>
        body { 
            font-family: Arial; 
            padding: 50px; 
        } 
        .container { 
            max-width: 300px; 
            margin: auto; 
        } 
        input { 
            width: 100%; 
            margin-bottom: 10px; 
            padding: 8px; 
        } 
        button { 
            width: 100%; 
            padding: 10px; 
        }
        .erro { 
            color: red; 
            margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login - Almoxarifado</h2> 

        <!-- Se houver algum erro de login, exibe a div com a mensagem -->
        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <!-- Formulário que envia os dados via POST para a própria página de login -->
        <form method="POST" action="index.php">
            <label>E-mail:</label>
            <input type="email" name="email" required>
            <label>Senha:</label>
            <input type="password" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>