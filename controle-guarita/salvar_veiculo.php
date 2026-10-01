<?php
include 'verificar_sessao.php';
include 'conexao.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = $_POST['placa'];
    try {
        $stmt_check = $pdo->prepare("SELECT placa FROM veiculos WHERE placa = :placa");
        $stmt_check->execute([':placa' => $placa]);
        if ($stmt_check->fetch()) {
            echo "<script>alert('Erro: A placa \\'" . htmlspecialchars($placa) . "\\' já está cadastrada.'); window.history.back();</script>";
            exit();
        }
        $sql = "INSERT INTO veiculos (marca, modelo, cor, placa) VALUES (:marca, :modelo, :cor, :placa)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':marca' => $_POST['marca'], ':modelo' => $_POST['modelo'], ':cor' => $_POST['cor'], ':placa' => $placa]);
        echo "<script>alert('Veículo cadastrado com sucesso!'); window.location.href='cadastro-veiculo.php';</script>";
    } catch (PDOException $e) {
        die("Erro ao cadastrar veículo: " . $e->getMessage());
    }
}
?>