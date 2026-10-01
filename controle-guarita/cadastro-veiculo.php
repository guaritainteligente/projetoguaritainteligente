<?php
include 'verificar_sessao.php';
include 'conexao.php';
$termo_busca = $_GET['busca'] ?? '';
$sql = "SELECT * FROM veiculos WHERE ativo = 1";
$params = [];
if (!empty($termo_busca)) {
    $sql .= " AND (marca LIKE :busca OR modelo LIKE :busca OR cor LIKE :busca OR placa LIKE :busca)";
    $params[':busca'] = '%' . $termo_busca . '%';
}
$sql .= " ORDER BY marca, modelo";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$veiculos_cadastrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Veículo</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header"><h3>Cadastro de Veículo</h3></div>
            <div class="card-body">
                <form action="salvar_veiculo.php" method="POST">
                    <div class="mb-3"><label for="marca" class="form-label">Marca</label><input type="text" class="form-control" id="marca" name="marca" placeholder="Digite a marca do veículo" required></div>
                    <div class="mb-3"><label for="modelo" class="form-label">Modelo</label><input type="text" class="form-control" id="modelo" name="modelo" placeholder="Digite o modelo do veículo" required></div>
                    <div class="mb-3"><label for="cor" class="form-label">Cor</label><input type="text" class="form-control" id="cor" name="cor" placeholder="Digite a cor do veículo" required></div>
                    <div class="mb-3"><label for="placa" class="form-label">Placa (Apenas letras e números)</label><input type="text" class="form-control" id="placa" name="placa" placeholder="Digite a placa do veículo" required></div>
                    <button type="submit" class="btn btn-primary">Cadastrar Veículo</button>
                </form>
            </div>
        </div>
        <div class="card mt-5 mb-5">
            <div class="card-header"><h4>Veículos Cadastrados</h4></div>
            <div class="card-body">
                <form action="cadastro-veiculo.php" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="search" class="form-control" name="busca" placeholder="Pesquisar por marca, modelo, cor, placa..." value="<?= htmlspecialchars($termo_busca) ?>">
                        <button class="btn btn-primary" type="submit">Pesquisar</button>
                        <a href="cadastro-veiculo.php" class="btn btn-secondary">Limpar</a>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead><tr><th>Marca</th><th>Modelo</th><th>Cor</th><th>Placa</th><th class="text-end">Ações</th></tr></thead>
                        <tbody>
                            <?php if (empty($veiculos_cadastrados)): ?>
                                <tr><td colspan="5" class="text-center">Nenhum resultado encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach ($veiculos_cadastrados as $veiculo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($veiculo['marca']) ?></td>
                                    <td><?= htmlspecialchars($veiculo['modelo']) ?></td>
                                    <td><?= htmlspecialchars($veiculo['cor']) ?></td>
                                    <td><?= htmlspecialchars($veiculo['placa']) ?></td>
                                    <td class="text-end">
                                        <a href="editar-veiculo.php?id=<?= $veiculo['id'] ?>" class="btn btn-sm btn-warning me-2">Editar</a>
                                        <a href="apagar.php?tabela=veiculos&id=<?= $veiculo['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Apagar</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>