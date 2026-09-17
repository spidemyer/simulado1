CREATE DATABASE saep_db;

CREATE TABLE Usuario (
    id_usuario SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE Produto (
    id_produto SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    material VARCHAR(100),
    tamanho VARCHAR(50),
    peso DECIMAL(10,2),
    estoque_minimo INT NOT NULL,
    quantidade_atual INT NOT NULL DEFAULT 0
);


CREATE TABLE Movimentacao_Estoque (
    id_movimentacao SERIAL PRIMARY KEY,
    id_produto INT NOT NULL,
    id_usuario INT NOT NULL,
    tipo VARCHAR(20) CHECK (tipo IN ('Entrada', 'Saída')) NOT NULL,
    quantidade INT NOT NULL,
    data_movimentacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_produto) REFERENCES Produto(id_produto) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
);

INSERT INTO Usuario (nome, email, senha) VALUES
('João Silva', 'joao.silva@almoxarifado.com', 'senha123'),
('Maria Oliveira', 'maria.oliveira@almoxarifado.com', 'senha456'),
('Carlos Souza', 'carlos.souza@almoxarifado.com', 'senha789');

INSERT INTO Produto (nome, descricao, material, tamanho, peso, estoque_minimo, quantidade_atual) VALUES
('Martelo de Unha', 'Martelo com cabo de madeira para uso geral', 'Aço e Madeira', '25 cm', 0.50, 10, 50),
('Chave de Fenda Isolada', 'Chave de fenda com revestimento isolante elétrico', 'Aço Cromo Vanádio e Borracha', '15 cm', 0.15, 20, 25),
('Alicate Universal', 'Alicate para corte e aperto com cabo emborrachado', 'Aço Carbono e Borracha', '20 cm', 0.35, 15, 30);

INSERT INTO Movimentacao_Estoque (id_produto, id_usuario, tipo, quantidade, data_movimentacao) VALUES
(1, 1, 'Entrada', 50, '2024-08-16 08:30:00'),
(2, 2, 'Entrada', 35, '2024-08-16 09:15:00'),
(2, 3, 'Saída', 10, '2024-08-17 14:20:00');