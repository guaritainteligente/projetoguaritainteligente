<?php
include 'verificar_sessao.php';
include 'conexao.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pessoa = $_POST['id_pessoa'];
    $id_veiculo = $_POST['id_veiculo'];
    $tipo = $_POST['tipo'];
    $id_usuario = $_SESSION['usuario_id'];
    try {
        $sql = "INSERT INTO registros (id_pessoa, id_veiculo, id_usuario, tipo) VALUES (:id_pessoa, :id_veiculo, :id_usuario, :tipo)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_pessoa', $id_pessoa);
        $stmt->bindParam(':id_veiculo', $id_veiculo);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        die("Erro ao registrar movimento: " . $e->getMessage());
    }
}
?>