<?php
    // Inicia a sessão PHP
    session_start();

    // Importa o arquivo de conexão com o banco de dados
    require 'conexao.php';

    // Se o usuário não estiver logado, redireciona para a tela inicial e encerra
    if (!isset($_SESSION['id_usuario'])) { header("Location: index.php"); exit; }

    // Inicializa a variável de alerta JS vazia para uso posterior
    $alerta_js = '';

    // Verifica se o formulário foi enviado através do método POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Recebe o ID do produto selecionado no formulário
        $id_produto = $_POST['id_produto'];

        // Recebe o tipo de movimentação (entrada ou saída)
        $tipo = $_POST['tipo'];

        // Recebe a quantidade digitada e converte obrigatoriamente para um número inteiro
        $quantidade = (int)$_POST['quantidade'];

        // Recebe a data e hora informadas no formulário
        $data_movimentacao = $_POST['data_movimentacao'];

        // Pega o ID do usuário atualmente logado na sessão
        $id_usuario = $_SESSION['id_usuario'];

        // Prepara a consulta no banco de dados para buscar o estoque atual e mínimo do produto escolhido
        $stmt = $pdo->prepare("SELECT quantidade_atual, estoque_minimo FROM Produto WHERE id_produto = ?");

        // Executa a consulta passando o ID do produto
        $stmt->execute([$id_produto]);

        // Busca o resultado e o organiza como um array associativo
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        // Copia a quantidade atual do produto para a variável de novo saldo
        $nova_qtd = $produto['quantidade_atual'];

        // Se a movimentação for "Entrada", soma a quantidade informada ao saldo atual
        if ($tipo == 'Entrada') {
            $nova_qtd += $quantidade;

        // Se for "Saída", subtrai a quantidade do saldo atual
        } elseif ($tipo == 'Saída') {
            $nova_qtd -= $quantidade;

            // Se o novo saldo ficar menor do que o estoque mínimo do produto
            if ($nova_qtd < $produto['estoque_minimo']) {
                // Prepara uma mensagem em JavaScript para ser exibida na página
                $alerta_js = "<script>alert('ALERTA: O estoque do produto ficou abaixo do mínimo configurado :(');</script>";
            }
        }

        // Prepara a instrução SQL para atualizar o saldo atual do produto no banco de dados
        $stmtUpdate = $pdo->prepare("UPDATE Produto SET quantidade_atual = ? WHERE id_produto = ?");

        // Executa a atualização gravando o novo saldo
        $stmtUpdate->execute([$nova_qtd, $id_produto]);

        // Prepara a instrução SQL para registrar o histórico dessa movimentação
        $stmtHist = $pdo->prepare("INSERT INTO Movimentacao_Estoque (id_produto, id_usuario, tipo, quantidade, data_movimentacao) VALUES (?, ?, ?, ?, ?)");

        // Executa o registro gravando quem fez, o quê, quanto e quando
        $stmtHist->execute([$id_produto, $id_usuario, $tipo, $quantidade, $data_movimentacao]);
    }

    // Busca todos os produtos gravados na tabela 'Produto'
    $stmt = $pdo->query("SELECT * FROM Produto");

    // Guarda a lista de todos os produtos retornados em um array
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Salva na variável $n a quantidade total de produtos encontrados
    $n = count($produtos);

    // Loop,executa N-1 vezes
    for ($i = 0; $i < $n - 1; $i++) {
        // Loop interno para comparar os produtos lado a lado
        for ($j = 0; $j < $n - $i - 1; $j++) {
            // Compara se o nome do produto atual vem depois do nome do próximo produto
            if (strcasecmp($produtos[$j]['nome'], $produtos[$j+1]['nome']) > 0) {
                // Salva o produto atual temporariamente
                $temp = $produtos[$j];

                // Coloca o próximo produto na posição do atual
                $produtos[$j] = $produtos[$j+1];

                // Coloca o produto temporário na próxima posição (efetua a troca)
                $produtos[$j+1] = $temp;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR"> 
<head>
    <meta charset="UTF-8">
    <title>Gestão de Estoque</title>
    <style>
        body { font-family: Arial; padding: 20px; } 
        table { width: 100%; border-collapse: collapse; margin-top: 20px; } 
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } 
        .btn-voltar { display: block; margin-bottom: 20px; } 
        .form-mov { background: #f9f9f9; padding: 15px; border: 1px solid #ccc; }
    </style>
    <!-- Imprime o script do alert JavaScript (caso exista mensagem de estoque mínimo) -->
    <?= $alerta_js ?>
</head>
<body>
    <!-- Link para retornar para a página principal -->
    <a href="principal.php" class="btn-voltar">← Voltar para Principal</a>
    <h2>Gestão de Estoque</h2>

    <!-- Bloco visual contendo o formulário -->
    <div class="form-mov">
        <h3>Registrar Movimentação</h3>

        <!-- Formulário que envia os dados via POST para a própria página (estoque.php) -->
        <form method="POST" action="estoque.php">
            <label>Produto:</label>
            <!-- Menu suspenso para escolher o produto (campo obrigatório) -->
            <select name="id_produto" required>
                <option value="">Selecione...</option>
                <!-- Loop PHP para criar uma opção (<option>) para cada produto ordenado -->
                <?php foreach ($produtos as $p): ?>
                    <!-- Cria a opção com o ID no valor, exibindo Nome (com proteção contra código malicioso) e Saldo Atual -->
                    <option value="<?= $p['id_produto'] ?>"><?= htmlspecialchars($p['nome']) ?> (Atual: <?= $p['quantidade_atual'] ?>)</option>
                <?php endforeach; ?>
            </select>
            
            <label>Tipo:</label>
            <!-- Menu para selecionar se é Entrada ou Saída de estoque -->
            <select name="tipo" required>
                <option value="Entrada">Entrada</option>
                <option value="Saída">Saída</option>
            </select>

            <label>Quantidade:</label>
            <!-- Campo numérico obrigatório, aceitando no mínimo o valor 1 -->
            <input type="number" name="quantidade" min="1" required>

            <label>Data:</label>
            <!-- Campo para seleção de data e horário -->
            <input type="datetime-local" name="data_movimentacao" required>

            <!-- Botão que faz o envio do formulário -->
            <button type="submit">Salvar Movimentação</button>
        </form>
    </div>

    <h3>Lista de Produtos (Ordem Alfabética)</h3>
    <!-- Tabela para exibição do estoque ordenado -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Estoque Atual</th>
            <th>Estoque Mínimo</th>
        </tr>

        <!-- Loop PHP para gerar uma linha (<tr>) na tabela para cada produto -->
        <?php foreach ($produtos as $p): ?>
        <tr>
            <!-- Exibe o ID do produto -->
            <td><?= $p['id_produto'] ?></td>
            <!-- Exibe o nome do produto tratado com htmlspecialchars para segurança -->
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <!-- Exibe a quantidade atual em negrito -->
            <td><strong><?= $p['quantidade_atual'] ?></strong></td>
            <!-- Exibe a quantidade mínima do estoque -->
            <td><?= $p['estoque_minimo'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>