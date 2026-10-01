<?php

$senha_para_criptografar = 'teste';

$hash = password_hash($senha_para_criptografar, PASSWORD_DEFAULT);

echo "<h1>Nova Senha Criptografada</h1>";
echo "<textarea rows='4' cols='80' readonly>" . htmlspecialchars($hash) . "</textarea>";
?>