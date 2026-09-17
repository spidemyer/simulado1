<?php
    // Inicia a sessão PHP
    session_start();

    //Faz o logout do usuário, destruindo as informações salvas
    session_destroy();

    // Redireciona o navegador de volta para a página de login (index.php)
    header("Location: index.php");

    // Encerraa execução para garantir o redirecionamento
    exit;
?>