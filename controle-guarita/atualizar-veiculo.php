<?php
include 'verificar_sessao.php';
include 'conexao.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $placa = $_POST['placa'];
    try {
        $stmt_check = $pdo->prepare("SELECT id FROM veiculos WHERE placa = :placa AND id != :id");
        $stmt_check->execute([':placa' => $placa, ':id' => $id]);
        if ($stmt_check->fetch()) {
            echo "<script>alert('Erro: A placa informada já está cadastrada em outro veículo!'); window.history.back();</script>";
            exit();
        }
        $sql = "UPDATE veiculos SET marca = :marca, modelo = :modelo, cor = :cor, placa = :placa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':marca' => $_POST['marca'], ':modelo' => $_POST['modelo'], ':cor' => $_POST['cor'], ':placa' => $placa]);
        header("Location: cadastro-veiculo.php");
        exit();
    } catch (PDOException $e) {
        die("Erro ao atualizar os dados do veículo: " . $e->getMessage());
    }
} else {
    header("Location: cadastro-veiculo.php");
    exit();
}
?>