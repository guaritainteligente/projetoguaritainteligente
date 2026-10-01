<?php
include 'verificar_sessao.php';
include 'conexao.php';
if (!isset($_GET['id'])) { header("Location: cadastro-pessoa.php"); exit(); }
$id = $_GET['id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM pessoas WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pessoa) { header("Location: cadastro-pessoa.php"); exit(); }
} catch (PDOException $e) {
    die("Erro ao buscar dados da pessoa: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pessoa</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header"><h3>Editar Pessoa</h3></div>
            <div class="card-body">
                <form action="atualizar-pessoa.php" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($pessoa['id']) ?>">
                    <div class="mb-3"><label for="nome" class="form-label">Nome Completo</label><input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($pessoa['nome']) ?>" required></div>
                    <div class="mb-3">
                        <label class="form-label">É Motorista de Transportadora?</label>
                        <div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="is_transportadora" id="transpSim" value="1" <?= $pessoa['is_transportadora'] ? 'checked' : '' ?>><label class="form-check-label" for="transpSim">Sim</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="is_transportadora" id="transpNao" value="0" <?= !$pessoa['is_transportadora'] ? 'checked' : '' ?>><label class="form-check-label" for="transpNao">Não</label></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="documento" class="form-label">Documento (CPF - 11 dígitos)</label>
                        <input type="text" class="form-control" id="documento" name="documento" value="<?= htmlspecialchars($pessoa['documento']) ?>" required maxlength="11" pattern="\d{11}" title="O documento deve conter exatamente 11 números.">
                    </div>
                    <div class="mb-3"><label for="empresa" class="form-label">Empresa (Opcional)</label><input type="text" class="form-control" id="empresa" name="empresa" value="<?= htmlspecialchars($pessoa['empresa']) ?>"></div>
                    <div class="mb-3"><label for="telefone" class="form-label">Telefone</label><input type="tel" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($pessoa['telefone']) ?>"></div>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                    <a href="cadastro-pessoa.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>