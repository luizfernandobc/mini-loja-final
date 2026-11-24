<?php
// funcionario.php: o CONTROLE da área do funcionário.
// a sessão é a "memória" do servidor sobre você; tem que vir antes de qualquer HTML
session_start();
require __DIR__ . '/banco.php';

// clicou em "Sair"? esquece quem estava logado e volta
if (isset($_GET['sair'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: funcionario.php');
    exit;
}

$erro = '';
$aviso = '';

// NOVO: funcionário novo só entra pelas mãos de quem já está logado
// (a loja sem ninguém? o primeiro cadastro é livre: é o do administrador)
$podeCadastrar = isset($_SESSION['funcionario']) || contarFuncionarios() === 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // o campo escondido "acao" diz qual formulário foi enviado
    $acao = $_POST['acao'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($acao === 'entrar') {
        // password_verify compara a senha digitada com o hash guardado
        $funcionario = buscarFuncionario($email);
        if ($funcionario && password_verify($senha, $funcionario['senha'])) {
            session_regenerate_id(true);  // crachá novo a cada entrada
            $_SESSION['funcionario'] = $funcionario['nome'];
            header('Location: cadastro.php');
            exit;
        }
        // não conta qual dos dois errou: assim ninguém descobre e-mails cadastrados
        $erro = 'E-mail ou senha errados.';
    }

    if ($acao === 'cadastrar') {
        if (!$podeCadastrar) {
            $erro = 'Só um funcionário logado cadastra outro.';
        } elseif ($nome === '') {
            $erro = 'Escreva o seu nome.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Esse e-mail não parece um e-mail.';
        } elseif (strlen($senha) < 8) {
            $erro = 'A senha precisa de 8 caracteres ou mais.';
        } elseif (buscarFuncionario($email)) {
            $erro = 'Esse e-mail já tem cadastro.';
        } else {
            inserirFuncionario($nome, $email, password_hash($senha, PASSWORD_DEFAULT));
            $aviso = 'Cadastro feito, ' . $nome . '! Agora é só entrar.';
        }
    }
}

// quem está logado (vazio = ninguém)
$logado = $_SESSION['funcionario'] ?? '';

// NOVO: depois de um cadastro, a conta muda (a loja já não está vazia)
$podeCadastrar = $logado !== '' || contarFuncionarios() === 0;

require __DIR__ . '/tela-funcionario.php';
