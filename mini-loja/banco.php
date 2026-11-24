<?php
// banco.php: o MODELO.
// Conecta no banco e guarda as funções que falam com ele. Todo o SQL mora aqui.

function conectar(): PDO
{
    // o banco é um arquivo só; se ele não existe, o PHP cria
    $pdo = new PDO('sqlite:' . __DIR__ . '/mini-loja.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // a tabela nasce sozinha na primeira vez
    $pdo->exec('CREATE TABLE IF NOT EXISTS produtos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        preco REAL NOT NULL
    )');

    // a coluna da foto (aula 14). A tabela já existia? O ALTER TABLE acrescenta a coluna, uma vez só
    $colunas = $pdo->query('PRAGMA table_info(produtos)')->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('imagem', $colunas)) {
        $pdo->exec('ALTER TABLE produtos ADD COLUMN imagem TEXT');
    }

    // a tabela dos funcionários. A senha nunca entra pura: entra o "hash" dela
    $pdo->exec('CREATE TABLE IF NOT EXISTS funcionarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL
    )');

    // banco vazio? entram três produtos de exemplo, para a lista não começar em branco
    $total = $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
    if ($total == 0) {
        $pdo->exec("INSERT INTO produtos (nome, preco) VALUES
            ('Placa ESP32', 39.90),
            ('Arduino Uno', 59.90),
            ('Sensor de temperatura DHT11', 12.50)");
    }

    return $pdo;
}

// R: devolve todos os produtos, em ordem alfabética (agora com a foto)
function listarProdutos(): array
{
    $pdo = conectar();
    return $pdo->query('SELECT id, nome, preco, imagem FROM produtos ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);
}

// C: grava um produto novo; a imagem é só o NOME do arquivo (a foto fica na pasta uploads)
function inserirProduto(string $nome, float $preco, string $imagem): void
{
    $pdo = conectar();
    $stmt = $pdo->prepare('INSERT INTO produtos (nome, preco, imagem) VALUES (:nome, :preco, :imagem)');
    $stmt->execute([':nome' => $nome, ':preco' => $preco, ':imagem' => $imagem]);
}

// D: apaga o produto pelo id (o número que o banco deu a ele)
function excluirProduto(int $id): void
{
    $pdo = conectar();
    $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

// busca um produto só, para preencher o formulário de edição
function buscarProduto(int $id): ?array
{
    $pdo = conectar();
    $stmt = $pdo->prepare('SELECT id, nome, preco, imagem FROM produtos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// U: troca o nome, o preço e a foto do produto
function atualizarProduto(int $id, string $nome, float $preco, string $imagem): void
{
    $pdo = conectar();
    $stmt = $pdo->prepare('UPDATE produtos SET nome = :nome, preco = :preco, imagem = :imagem WHERE id = :id');
    $stmt->execute([':nome' => $nome, ':preco' => $preco, ':imagem' => $imagem, ':id' => $id]);
}

// grava um funcionário; a senha já chega embaralhada (o hash)
function inserirFuncionario(string $nome, string $email, string $hash): void
{
    $pdo = conectar();
    $stmt = $pdo->prepare('INSERT INTO funcionarios (nome, email, senha) VALUES (:nome, :email, :senha)');
    $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $hash]);
}

// procura o funcionário pelo e-mail (devolve null se não tem)
function buscarFuncionario(string $email): ?array
{
    $pdo = conectar();
    $stmt = $pdo->prepare('SELECT id, nome, email, senha FROM funcionarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// NOVO: quantos funcionários a loja tem (zero = o primeiro cadastro é livre)
function contarFuncionarios(): int
{
    $pdo = conectar();
    return (int) $pdo->query('SELECT COUNT(*) FROM funcionarios')->fetchColumn();
}
