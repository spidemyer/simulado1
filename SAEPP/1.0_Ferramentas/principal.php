<?php
    // Inicia a sessão PHP
    session_start();

    // Verifica se a sessão do usuário não está logado
    if (!isset($_SESSION['id_usuario'])) {
        // Lleva o usuário não autenticado para a página de login
        header("Location: index.php");

        // Encerra a execução para impedir o acesso indevido à página
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>SAEP - Principal</title>
    <style>
        body { 
            font-family: Arial; 
            padding: 20px; 
        } 
        .menu a { 
            margin-right: 15px; 
            padding: 10px; 
            background: #007bff; 
            color: #fff; 
            text-decoration: none; 
            border-radius: 4px; 
        }
    </style>
</head>
<body>
    <h1>Sistema de Gestão de Almoxarifado</h1>
    <!-- Exibe uma mensagem de boas-vindas com o nome do usuário logado -->
    <p>Bem-vindo(a), <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong>!</p>
    
    <!-- Bloco contendo os botões de navegação do sistema -->
    <div class="menu">
        <a href="produtos.php">Cadastro de Produto</a>
        <a href="estoque.php">Gestão de Estoque</a>
        <!-- Botão para encerrar a sessão do usuário -->
        <a href="logout.php" style="background: #dc3545;">Sair</a>
    </div>
</body>
</html>