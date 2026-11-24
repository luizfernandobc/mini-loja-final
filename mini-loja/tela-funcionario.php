<?php // tela-funcionario.php: a VISÃO da área do funcionário. Só o HTML ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Mini Loja · Funcionário</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <p><a href="index.html">← Página inicial</a></p>
  <h1>Área do funcionário</h1>

  <?php if ($erro): ?>
    <p style="color: firebrick"><strong>Não deu:</strong> <?= htmlspecialchars($erro) ?></p>
  <?php endif; ?>
  <?php if ($aviso): ?>
    <p style="color: green"><?= htmlspecialchars($aviso) ?></p>
  <?php endif; ?>

  <!-- já entrou? mostra quem é e o botão de sair -->
  <?php if ($logado): ?>
    <p>Olá, <strong><?= htmlspecialchars($logado) ?></strong>! <a href="cadastro.php">Cadastrar produtos</a> · <a href="funcionario.php?sair=1">Sair</a></p>
  <?php else: ?>
    <h2>Entrar</h2>
    <form method="post" action="funcionario.php">
      <input type="hidden" name="acao" value="entrar">

      <label for="email-entrar">E-mail</label>
      <input id="email-entrar" name="email" type="email" required>

      <label for="senha-entrar">Senha</label>
      <input id="senha-entrar" name="senha" type="password" required>

      <button type="submit">Entrar</button>
    </form>
  <?php endif; ?>

  <!-- NOVO: o formulário de cadastro só aparece para quem pode usar -->
  <?php if ($podeCadastrar): ?>
  <h2><?= $logado ? 'Cadastrar funcionário' : 'Primeiro acesso: cadastre o administrador' ?></h2>
  <form method="post" action="funcionario.php">
    <input type="hidden" name="acao" value="cadastrar">

    <label for="nome">Nome</label>
    <input id="nome" name="nome" required>

    <label for="email">E-mail</label>
    <input id="email" name="email" type="email" required>

    <label for="senha">Senha (8 caracteres ou mais)</label>
    <input id="senha" name="senha" type="password" minlength="8" required>

    <button type="submit">Cadastrar</button>
  </form>
  <?php endif; ?>
</body>
</html>
