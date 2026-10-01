<?php
session_start();
$erro = isset($_GET['erro']) ? "Usuário ou senha inválidos!" : "";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Controle de Guarita</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        html, body { height: 100%; }
        body { display: flex; align-items: center; justify-content: center; background-color: #f0f2f5; }
        .login-card { width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-header text-center"><h3>Controle de Guarita - Acesso</h3></div>
        <div class="card-body">
            <form action="validar_login.php" method="POST">
                <div class="mb-3"><label for="usuario" class="form-label">Usuário</label><input type="text" class="form-control" id="usuario" name="usuario" required></div>
                <div class="mb-3"><label for="senha" class="form-label">Senha</label><input type="password" class="form-control" id="senha" name="senha" required></div>
                <?php if ($erro): ?><div class="alert alert-danger" role="alert"><?= $erro ?></div><?php endif; ?>
                <div class="d-grid"><button type="submit" class="btn btn-primary">Entrar</button></div>
            </form>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>