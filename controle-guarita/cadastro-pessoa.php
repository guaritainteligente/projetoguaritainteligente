<?php
include 'verificar_sessao.php';
include 'conexao.php';
$termo_busca = $_GET['busca'] ?? '';
$sql = "SELECT * FROM pessoas WHERE ativo = 1";
$params = [];
if (!empty($termo_busca)) {
    $sql .= " AND (nome LIKE :busca OR documento LIKE :busca OR empresa LIKE :busca OR telefone LIKE :busca)";
    $params[':busca'] = '%' . $termo_busca . '%';
}
$sql .= " ORDER BY nome";
$stmt_pessoas = $pdo->prepare($sql);
$stmt_pessoas->execute($params);
$pessoas_cadastradas = $stmt_pessoas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pessoa</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header"><h3>Cadastro de Pessoa</h3></div>
            <div class="card-body">
                <form action="salvar_pessoa.php" method="POST">
                    <div class="mb-3"><label for="nome" class="form-label">Nome Completo</label><input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome da pessoa" required></div>
                    <div class="mb-3">
                        <label class="form-label">É motorista de transportadora?</label>
                        <div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="is_transportadora" id="transpSim" value="1"><label class="form-check-label" for="transpSim">Sim</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="is_transportadora" id="transpNao" value="0" checked><label class="form-check-label" for="transpNao">Não</label></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="documento" class="form-label">Documento CPF (Apenas números)</label>
                        <input type="text" class="form-control" id="documento" name="documento" required maxlength="11" pattern="\d{11}" placeholder="Digite os 11 números do CPF da pessoa" required>
                    </div>
                    <div class="mb-3"><label for="empresa" class="form-label">Empresa</label><input type="text" class="form-control" id="empresa" name="empresa" placeholder="Digite a empresa que a pessoa trabalha" required></div>
                    <div class="mb-3"><label for="telefone" class="form-label">Telefone</label><input type="tel" class="form-control" id="telefone" name="telefone" placeholder="Digite o telefone da pessoa"></div>
                    <button type="submit" class="btn btn-primary">Cadastrar Pessoa</button>
                </form>
            </div>
        </div>
        <div class="card mt-5 mb-5">
            <div class="card-header"><h4>Pessoas Cadastradas</h4></div>
            <div class="card-body">
                <form action="cadastro-pessoa.php" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="search" class="form-control" name="busca" placeholder="Pesquisar por nome, documento, empresa..." value="<?= htmlspecialchars($termo_busca) ?>">
                        <button class="btn btn-primary" type="submit">Pesquisar</button>
                        <a href="cadastro-pessoa.php" class="btn btn-secondary">Limpar</a>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead><tr><th>Nome</th><th>Documento</th><th>Empresa</th><th>Telefone</th><th>Transportadora?</th><th class="text-end">Ações</th></tr></thead>
                        <tbody>
                            <?php if (empty($pessoas_cadastradas)): ?>
                                <tr><td colspan="6" class="text-center">Nenhum resultado encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pessoas_cadastradas as $pessoa): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pessoa['nome']) ?></td>
                                    <td><?= htmlspecialchars($pessoa['documento']) ?></td>
                                    <td><?= htmlspecialchars($pessoa['empresa']) ?: 'N/A' ?></td>
                                    <td><?= htmlspecialchars($pessoa['telefone']) ?></td>
                                    <td><?= $pessoa['is_transportadora'] ? 'Sim' : 'Não' ?></td>
                                    <td class="text-end">
                                        <a href="editar-pessoa.php?id=<?= $pessoa['id'] ?>" class="btn btn-sm btn-warning me-2">Editar</a>
                                        <a href="apagar.php?tabela=pessoas&id=<?= $pessoa['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Apagar</a>
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