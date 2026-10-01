<?php
include 'verificar_sessao.php';
include 'conexao.php';
if (!isset($_GET['pessoa_id'])) {
    header("Location: index.php");
    exit();
}
$id_pessoa = $_GET['pessoa_id'];
$id_veiculo = !empty($_GET['veiculo_id']) ? $_GET['veiculo_id'] : null;
$tipo = 'saida';
$id_usuario = $_SESSION['usuario_id'];
try {
    $sql = "INSERT INTO registros (id_pessoa, id_veiculo, id_usuario, tipo) VALUES (:id_pessoa, :id_veiculo, :id_usuario, :tipo)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_pessoa', $id_pessoa, PDO::PARAM_INT);
    $stmt->bindParam(':id_veiculo', $id_veiculo, PDO::PARAM_INT);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->execute();
    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    die("Erro ao registrar a saída rápida: " . $e->getMessage());
}
?>