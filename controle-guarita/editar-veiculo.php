<?php
include 'verificar_sessao.php';
include 'conexao.php';
if (!isset($_GET['id'])) { header("Location: cadastro-veiculo.php"); exit(); }
$id = $_GET['id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = :id AND ativo = 1");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $veiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$veiculo) { header("Location: cadastro-veiculo.php"); exit(); }
} catch (PDOException $e) {
    die("Erro ao buscar dados do veículo: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Veículo</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header"><h3>Editar Veículo</h3></div>
            <div class="card-body">
                <form action="atualizar-veiculo.php" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($veiculo['id']) ?>">
                    <div class="mb-3"><label for="marca" class="form-label">Marca</label><input type="text" class="form-control" id="marca" name="marca" value="<?= htmlspecialchars($veiculo['marca']) ?>" required></div>
                    <div class="mb-3"><label for="modelo" class="form-label">Modelo</label><input type="text" class="form-control" id="modelo" name="modelo" value="<?= htmlspecialchars($veiculo['modelo']) ?>" required></div>
                    <div class="mb-3"><label for="cor" class="form-label">Cor</label><input type="text" class="form-control" id="cor" name="cor" value="<?= htmlspecialchars($veiculo['cor']) ?>" required></div>
                    <div class="mb-3"><label for="placa" class="form-label">Placa</label><input type="text" class="form-control" id="placa" name="placa" value="<?= htmlspecialchars($veiculo['placa']) ?>" required></div>
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                    <a href="cadastro-veiculo.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>