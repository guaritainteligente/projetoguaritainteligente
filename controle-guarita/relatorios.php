<?php
include 'verificar_sessao.php';
include 'conexao.php';

$stmt_pessoas = $pdo->query("SELECT id, nome FROM pessoas WHERE ativo = 1 ORDER BY nome");
$pessoas = $stmt_pessoas->fetchAll(PDO::FETCH_ASSOC);

$stmt_veiculos = $pdo->query("SELECT id, modelo, placa FROM veiculos WHERE ativo = 1 ORDER BY modelo");
$veiculos = $stmt_veiculos->fetchAll(PDO::FETCH_ASSOC);

$stmt_usuarios = $pdo->query("SELECT id, usuario FROM usuarios ORDER BY usuario");
$usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);


$registros = [];
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$hora_inicio = $_GET['hora_inicio'] ?? '';
$hora_fim = $_GET['hora_fim'] ?? '';
$pessoa_id = $_GET['pessoa_id'] ?? '';
$veiculo_id = $_GET['veiculo_id'] ?? '';
$empresa = $_GET['empresa'] ?? '';
$is_transportadora = $_GET['is_transportadora'] ?? '';
$usuario_id = $_GET['usuario_id'] ?? ''; // NOVO

$busca_realizada = isset($_GET['gerar_relatorio']);

if ($busca_realizada) {
    $sql = "
        SELECT 
            r.tipo, r.data_hora, p.nome AS nome_pessoa,
            p.empresa, p.is_transportadora,
            v.modelo AS modelo_veiculo, v.placa, u.usuario AS nome_usuario
        FROM registros r
        JOIN pessoas p ON r.id_pessoa = p.id
        LEFT JOIN veiculos v ON r.id_veiculo = v.id
        JOIN usuarios u ON r.id_usuario = u.id
    ";

    $where_clauses = [];
    $params = [];

    if (!empty($data_inicio) && !empty($data_fim)) {
        $where_clauses[] = "r.data_hora BETWEEN :inicio AND :fim";
        $params[':inicio'] = $data_inicio . " 00:00:00";
        $params[':fim'] = $data_fim . " 23:59:59";
    }
    if (!empty($hora_inicio) && !empty($hora_fim)) {
        $where_clauses[] = "TIME(r.data_hora) BETWEEN :hora_inicio AND :hora_fim";
        $params[':hora_inicio'] = $hora_inicio;
        $params[':hora_fim'] = $hora_fim;
    }
    if (!empty($pessoa_id)) { $where_clauses[] = "r.id_pessoa = :pessoa_id"; $params[':pessoa_id'] = $pessoa_id; }
    if (!empty($veiculo_id)) { $where_clauses[] = "r.id_veiculo = :veiculo_id"; $params[':veiculo_id'] = $veiculo_id; }
    if (!empty($empresa)) { $where_clauses[] = "p.empresa LIKE :empresa"; $params[':empresa'] = '%' . $empresa . '%'; }
    if ($is_transportadora !== '') { $where_clauses[] = "p.is_transportadora = :is_transportadora"; $params[':is_transportadora'] = $is_transportadora; }
    
    if (!empty($usuario_id)) { 
        $where_clauses[] = "r.id_usuario = :usuario_id"; 
        $params[':usuario_id'] = $usuario_id; 
    }

    if (!empty($where_clauses)) { $sql .= " WHERE " . implode(" AND ", $where_clauses); }
    $sql .= " ORDER BY r.data_hora DESC";
    
    $stmt_registros = $pdo->prepare($sql);
    $stmt_registros->execute($params);
    $registros = $stmt_registros->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Registros - Controle de Guarita</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/select2-bootstrap-5-theme.min.css" />
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">

        <div class="card mb-4">
            <div class="card-header"><h3>Relatório de Entradas e Saídas</h3></div>
            <div class="card-body">
                <form action="relatorios.php" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3"><label for="data_inicio" class="form-label">Data de Início</label><input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>"></div>
                        <div class="col-md-3"><label for="data_fim" class="form-label">Data de Fim</label><input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>"></div>
                        <div class="col-md-3"><label for="hora_inicio" class="form-label">Hora de Início</label><input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="<?= htmlspecialchars($hora_inicio) ?>"></div>
                        <div class="col-md-3"><label for="hora_fim" class="form-label">Hora de Fim</label><input type="time" class="form-control" id="hora_fim" name="hora_fim" value="<?= htmlspecialchars($hora_fim) ?>"></div>
                        
                        <div class="col-md-3"><label for="pessoa_id" class="form-label">Pessoa</label><select class="form-select" name="pessoa_id" id="pessoa_id"><option value="">Todas</option><?php foreach ($pessoas as $pessoa): ?><option value="<?= $pessoa['id'] ?>" <?= ($pessoa_id == $pessoa['id']) ? 'selected' : '' ?>><?= htmlspecialchars($pessoa['nome']) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-3"><label for="veiculo_id" class="form-label">Veículo</label><select class="form-select" name="veiculo_id" id="veiculo_id"><option value="">Todos</option><?php foreach ($veiculos as $veiculo): ?><option value="<?= $veiculo['id'] ?>" <?= ($veiculo_id == $veiculo['id']) ? 'selected' : '' ?>><?= htmlspecialchars($veiculo['placa'] . ' - ' . $veiculo['modelo']) ?></option><?php endforeach; ?></select></div>
                        <div class="col-md-3"><label for="empresa" class="form-label">Empresa</label><input type="text" class="form-control" name="empresa" id="empresa" placeholder="Digite o nome da empresa" value="<?= htmlspecialchars($empresa) ?>"></div>
                        <div class="col-md-3"><label for="is_transportadora" class="form-label">É transportadora?</label><select class="form-select" name="is_transportadora" id="is_transportadora"><option value="" <?= ($is_transportadora === '') ? 'selected' : '' ?>>Todos</option><option value="1" <?= ($is_transportadora === '1') ? 'selected' : '' ?>>Sim</option><option value="0" <?= ($is_transportadora === '0') ? 'selected' : '' ?>>Não</option></select></div>
                        
                        <div class="col-md-10">
                            <label for="usuario_id" class="form-label">Usuário que registrou</label>
                            <select class="form-select" name="usuario_id" id="usuario_id">
                                <option value="">Todos</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>" <?= ($usuario_id == $usuario['id']) ? 'selected' : '' ?>><?= htmlspecialchars($usuario['usuario']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="gerar_relatorio" value="1" class="btn btn-primary w-100">Gerar Relatório</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4>Resultados da Busca</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead><tr><th>Pessoa</th><th>Empresa</th><th>Transportadora?</th><th>Veículo</th><th>Tipo</th><th>Data/Hora</th><th>Usuário</th></tr></thead>
                        <tbody>
                            <?php if ($busca_realizada && empty($registros)): ?>
                                <tr><td colspan="7" class="text-center">Nenhum registro encontrado para os filtros selecionados.</td></tr>
                            <?php elseif (!$busca_realizada): ?>
                                <tr><td colspan="7" class="text-center">Por favor, selecione os filtros e clique em "Gerar Relatório".</td></tr>
                            <?php else: ?>
                                <?php foreach ($registros as $registro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($registro['nome_pessoa']) ?></td>
                                    <td><?= htmlspecialchars($registro['empresa']) ?: 'N/A' ?></td>
                                    <td><?= $registro['is_transportadora'] ? 'Sim' : 'Não' ?></td>
                                    <td><?= ($registro['modelo_veiculo'] && $registro['placa']) ? htmlspecialchars($registro['placa'] . ' - ' . $registro['modelo_veiculo']) : 'N/A' ?></td>
                                    <td><span class="badge bg-<?= $registro['tipo'] == 'entrada' ? 'success' : 'danger' ?>"><?= ucfirst($registro['tipo']) ?></span></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($registro['data_hora'])) ?></td>
                                    <td><?= htmlspecialchars($registro['nome_usuario']) ?></td>
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
            $('#pessoa_id').select2({ theme: 'bootstrap-5', placeholder: 'Selecione ou digite para pesquisar' });
            $('#veiculo_id').select2({ theme: 'bootstrap-5', placeholder: 'Selecione ou digite para pesquisar' });
            $('#usuario_id').select2({ theme: 'bootstrap-5', placeholder: 'Selecione um usuário' });
        });
    </script>
</body>
</html>