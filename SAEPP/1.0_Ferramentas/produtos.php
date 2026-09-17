<?php
    // Inicia a sessão PHP
    session_start();
    // Importa o arquivo de conexão com o banco de dados
    require 'conexao.php';

    // Se a sessão do usuário não existir (não logado), redireciona para a tela inicial e encerra o script
    if (!isset($_SESSION['id_usuario'])) { header("Location: index.php"); exit; }

    // Inicializa a variável para armazenar mensagens de aviso/sucesso
    $mensagem = '';

    // Verifica se foi enviado pela URL o 'excluir'
    if (isset($_GET['excluir'])) {
        // Recebe o ID do produto que deve ser excluído
        $id = $_GET['excluir'];

        // Prepara a consulta SQL para apagar o produto pelo ID
        $stmt = $pdo->prepare("DELETE FROM Produto WHERE id_produto = ?");

        // Executa a instrução de exclusão no banco de dados
        $stmt->execute([$id]);

        // Define a mensagem informando o sucesso da remoção
        $mensagem = "Produto excluído com sucesso!";
    }

    // Verifica se os dados do formulário foram enviados via POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id_produto'] ?? null; // Pega o ID enviado via formulário (se existir) ou define como nulo se for novo cadastro
        $nome = $_POST['nome'];
        $material = $_POST['material'];
        $estoque_minimo = $_POST['estoque_minimo'];

        // Valida se os campos obrigatórios (nome e estoque mínimo) estão em branco
        if (empty($nome) || empty($estoque_minimo)) {
            // Guarda a mensagem de erro de preenchimento
            $mensagem = "Erro: Campos Nome e Estoque Mínimo são obrigatórios.";
        } else {
            // Se o ID existir, é uma alteração de produto existente
            if ($id) {
                // Prepara a instrução SQL de atualização dos dados do produto
                $stmt = $pdo->prepare("UPDATE Produto SET nome=?, material=?, estoque_minimo=? WHERE id_produto=?");

                // Executa a atualização com os novos valores
                $stmt->execute([$nome, $material, $estoque_minimo, $id]);

                // Define a mensagem de sucesso para a alteração
                $mensagem = "Produto atualizado com sucesso!";
            } else {
                // Se não houver ID, é a criação de um novo produto (inicia com quantidade 0)
                $stmt = $pdo->prepare("INSERT INTO Produto (nome, material, estoque_minimo, quantidade_atual) VALUES (?, ?, ?, 0)");

                // Executa a inserção no banco de dados
                $stmt->execute([$nome, $material, $estoque_minimo]);

                // Define a mensagem de sucesso para o cadastro
                $mensagem = "Produto cadastrado com sucesso!";
            }
        }
    }

    // Pega o termo buscado via GET ou define como texto vazio caso não haja busca
    $termo = $_GET['busca'] ?? '';

    // Se o usuário digitou algum texto de busca
    if ($termo) {
        // Prepara a consulta buscando produtos com nomes semelhantes ao digitado
        $stmt = $pdo->prepare("SELECT * FROM Produto WHERE nome ILIKE ?");

        // Executa a busca adicionando o caractere % antes e depois do termo
        $stmt->execute(['%' . $termo . '%']);
    } else {
        // Se nenhuma busca foi feita, traz todos os produtos cadastrados
        $stmt = $pdo->query("SELECT * FROM Produto");
    }

    // Armazena a lista obtida em um array
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inicializa a variável de produto em edição como nula
    $produtoEdit = null;

    // Se a opção de editar foi clicada no link da tabela (passa parâmetro 'editar' via GET)
    if (isset($_GET['editar'])) {
        // Prepara a consulta para carregar as informações do produto selecionado
        $stmt = $pdo->prepare("SELECT * FROM Produto WHERE id_produto = ?");

        // Executa a busca passando o ID do produto
        $stmt->execute([$_GET['editar']]);

        // Carrega os dados do produto para preenchimento dos campos do formulário
        $produtoEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produtos</title>
    <style>
        body { 
            font-family: Arial; 
            padding: 20px; 
        } 
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        } th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        } 
        .alert { 
            color: green; 
            font-weight: bold; 
        } 
        .btn-voltar { 
            display: block;
            margin-bottom: 20px; }
    </style>
</head>
<body>
    <!-- Link para retornar para a página principal -->
    <a href="principal.php" class="btn-voltar">← Voltar para Principal</a>
    <h2>Cadastro de Produto</h2>
    
    <!-- Exibe a mensagem de aviso (sucesso/erro) caso ela não esteja vazia -->
    <?php if ($mensagem) echo "<p class='alert'>$mensagem</p>"; ?>

    <!-- Formulário para envio via POST -->
    <form method="POST" action="produtos.php">
        <!-- Campo oculto para enviar o ID em caso de edição -->
        <input type="hidden" name="id_produto" value="<?= $produtoEdit['id_produto'] ?? '' ?>">
        <!-- Campos de informações -->
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= $produtoEdit['nome'] ?? '' ?>" required>
        <label>Material:</label>
        <input type="text" name="material" value="<?= $produtoEdit['material'] ?? '' ?>">
        <label>Estoque Mínimo:</label>
        <input type="number" name="estoque_minimo" value="<?= $produtoEdit['estoque_minimo'] ?? '' ?>" required>

        <!-- Botão que altera seu rótulo -->
        <button type="submit"><?= $produtoEdit ? 'Salvar Edição' : 'Cadastrar' ?></button>
    </form>
    <hr>
    <h3>Listagem de Produtos</h3>

    <!-- Formulário de pesquisa para filtrar produtos por nome via método GET -->
    <form method="GET" action="produtos.php">
        <!-- Campo para digitar o nome buscado -->
        <input type="text" name="busca" placeholder="Buscar por nome..." value="<?= htmlspecialchars($termo) ?>">
        <button type="submit">Buscar</button>
    </form>

    <!-- Tabela para exibição dos produtos -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Material</th>
            <th>Estq. Mínimo</th>
            <th>Ações</th></tr>

        <!-- Loop PHP para retornr cada produto da pesquisa -->
        <?php foreach ($produtos as $p): ?>
        <tr>
            <td><?= $p['id_produto'] ?></td>

            <!-- Exibe o nome e descrição do produto tratado com htmlspecialchars -->
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td><?= htmlspecialchars($p['material']) ?></td>

            <!-- Exibe o limite mínimo de estoque do produto -->
            <td><?= $p['estoque_minimo'] ?></td>

            <!-- Coluna de botões para ações do registro -->
            <td>
                <!-- Link que recarrega a página enviando o ID via GET no formulário para edição -->
                <a href="produtos.php?editar=<?= $p['id_produto'] ?>">Editar</a> | 

                <!-- Link de exclusão enviando o ID via GET com caixa de confirmação em JS -->
                <a href="produtos.php?excluir=<?= $p['id_produto'] ?>" onclick="return confirm('Tem certeza?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>