<?php
    // Conexão com o banco de dados
    $host = 'localhost';
    $dbname = 'saep_db';
    $user = 'postgres';
    $password = 'postgres';

    // Verificação da conexão
    try {
        $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Conexão bem sucedida
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
?>