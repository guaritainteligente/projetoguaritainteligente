<?php
include 'verificar_sessao.php';
include 'conexao.php';

$stmt_pessoas = $pdo->query("SELECT id, nome FROM pessoas WHERE ativo = 1 ORDER BY nome");
$pessoas = $stmt_pessoas->fetchAll(PDO::FETCH_ASSOC);

$stmt_veiculos = $pdo->query("SELECT id, modelo, placa FROM veiculos WHERE ativo = 1 ORDER BY modelo");
$veiculos = $stmt_veiculos->fetchAll(PDO::FETCH_ASSOC);

$termo_busca = $_GET['busca'] ?? '';
date_default_timezone_set('America/Sao_Paulo');
$hora_de_inicio = "07:30:00";
if (date('H:i:s') < $hora_de_inicio) {
    $data_de_corte = date('Y-m-d', strtotime('-1 day')) . ' ' . $hora_de_inicio;
} else {
    $data_de_corte = date('Y-m-d') . ' ' . $hora_de_inicio;
}
$sql = "
    SELECT r.tipo, r.data_hora, p.id AS pessoa_id, p.nome AS nome_pessoa,
           p.empresa, p.is_transportadora,
           v.id AS veiculo_id, v.modelo AS modelo_veiculo, v.placa,
           u.usuario AS nome_usuario
    FROM registros r
    JOIN pessoas p ON r.id_pessoa = p.id
    LEFT JOIN veiculos v ON r.id_veiculo = v.id
    JOIN usuarios u ON r.id_usuario = u.id
";
$where_clauses = ["r.data_hora >= :data_corte"];
$params = [':data_corte' => $data_de_corte];
if (!empty($termo_busca)) {
    $where_clauses[] = "(p.nome LIKE :busca OR v.placa LIKE :busca OR u.usuario LIKE :busca)";
    $params[':busca'] = '%' . $termo_busca . '%';
}
$sql .= " WHERE " . implode(" AND ", $where_clauses);
$sql .= " ORDER BY r.data_hora DESC";
$stmt_registros = $pdo->prepare($sql);
$stmt_registros->execute($params);
$registros = $stmt_registros->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Guarita</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/select2-bootstrap-5-theme.min.css" />
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
    
        <div class="card">
            <div class="card-header"><h3>Controle de Entrada e Saída</h3></div>
            <div class="card-body">
                <form action="registrar_movimento.php" method="POST">
                    <div class="mb-3">
                        <label for="pessoa" class="form-label">Selecione a Pessoa</label>
                        <select class="form-select" id="pessoa" name="id_pessoa" required>
                            <option></option>
                            <?php foreach ($pessoas as $pessoa): ?>
                                <option value="<?= htmlspecialchars($pessoa['id']) ?>"><?= htmlspecialchars($pessoa['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="veiculo" class="form-label">Selecione o Veículo</label>
                        <select class="form-select" id="veiculo" name="id_veiculo" required>
                            <option></option>
                            <?php foreach ($veiculos as $veiculo): ?>
                                <option value="<?= htmlspecialchars($veiculo['id']) ?>"><?= htmlspecialchars($veiculo['placa'] . ' - ' . $veiculo['modelo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" name="tipo" value="entrada" class="btn btn-success me-md-2">Registrar Entrada</button>
                        <button type="submit" name="tipo" value="saida" class="btn btn-danger">Registrar Saída</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-5 mb-5">
            <div class="card-header"><h4>Registros do Dia</h4></div>
            <div class="card-body">
                <form action="index.php" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="search" class="form-control" name="busca" placeholder="Pesquisar por pessoa, placa ou usuário..." value="<?= htmlspecialchars($termo_busca) ?>">
                        <button class="btn btn-primary" type="submit">Pesquisar</button>
                        <a href="index.php" class="btn btn-secondary">Limpar</a>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Pessoa</th><th>Empresa</th><th>Transportadora?</th><th>Veículo</th><th>Tipo</th><th>Data/Hora</th><th>Usuário</th><th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ja_processado = [];
                            if (empty($registros)): ?>
                                <tr><td colspan="8" class="text-center">Nenhum resultado encontrado.</td></tr>
                            <?php else: ?>
                                <?php foreach ($registros as $registro):
                                    $chave_unica = $registro['pessoa_id'] . '-' . ($registro['veiculo_id'] ?? '0');
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($registro['nome_pessoa']) ?></td>
                                    <td><?= htmlspecialchars($registro['empresa']) ?: 'N/A' ?></td>
                                    <td><?= $registro['is_transportadora'] ? 'Sim' : 'Não' ?></td>
                                    <td><?= ($registro['placa'] && $registro['modelo_veiculo']) ? htmlspecialchars($registro['placa'] . ' - ' . $registro['modelo_veiculo']) : 'N/A' ?></td>
                                    <td><span class="badge bg-<?= $registro['tipo'] == 'entrada' ? 'success' : 'danger' ?>"><?= ucfirst($registro['tipo']) ?></span></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($registro['data_hora'])) ?></td>
                                    <td><?= htmlspecialchars($registro['nome_usuario']) ?></td>
                                    <td class="text-center">
                                        <?php
                                        if ($registro['tipo'] == 'entrada' && !in_array($chave_unica, $ja_processado)) {
                                            echo '<a href="registrar_saida_rapida.php?pessoa_id=' . $registro['pessoa_id'] . '&veiculo_id=' . $registro['veiculo_id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Confirmar saída para ' . htmlspecialchars(addslashes($registro['nome_pessoa'])) . '?\');">Registrar Saída</a>';
                                        }
                                        $ja_processado[] = $chave_unica;
                                        ?>
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

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#pessoa').select2({
                theme: 'bootstrap-5',
                placeholder: 'Digite para pesquisar uma pessoa...'
            });
            $('#veiculo').select2({
                theme: 'bootstrap-5',
                placeholder: 'Digite para pesquisar um veículo...'
            });
        });
    </script>
</body>
</html>