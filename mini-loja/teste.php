<?php
// teste.php: confere o ambiente antes de começar.
// 1. a versão do PHP já tem a correção do PHP2Shell?
// 2. o SQLite está ligado?

// as primeiras versões corrigidas de cada série (21/10/2026)
$corrigidas = ['8.5' => '8.5.12', '8.4' => '8.4.27', '8.3' => '8.3.36', '8.2' => '8.2.35'];
$serie = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
$minima = $corrigidas[$serie] ?? null;
$phpOk = $minima !== null && version_compare(PHP_VERSION, $minima, '>=');

$sqliteOk = extension_loaded('pdo_sqlite');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Teste do ambiente</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <h1>Teste do ambiente</h1>

  <p>PHP <?= PHP_VERSION ?>:
    <?= $phpOk ? 'atualizado, com a correção do PHP2Shell.' : 'versão antiga, sem a correção de outubro de 2026. Dá para estudar: só clique em Stop no Apache no fim da aula.' ?></p>

  <p>SQLite:
    <?= $sqliteOk ? 'ligado. Pode começar!' : 'DESLIGADO. Ligue o pdo_sqlite no php.ini (veja o passo a passo da aula).' ?></p>
</body>
</html>
