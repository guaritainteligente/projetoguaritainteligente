<?php
include 'verificar_sessao.php';
include 'conexao.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $is_transportadora = $_POST['is_transportadora'];
    $documento = $_POST['documento'];
    $empresa = !empty($_POST['empresa']) ? $_POST['empresa'] : null;
    $telefone = $_POST['telefone'];
    if (!ctype_digit($documento) || strlen($documento) != 11) {
        echo "<script>alert('Erro: O documento deve conter exatamente 11 dígitos numéricos.'); window.history.back();</script>";
        exit();
    }
    try {
        $stmt_check = $pdo->prepare("SELECT id FROM pessoas WHERE (nome = :nome OR documento = :documento) AND id != :id");
        $stmt_check->execute([':nome' => $nome, ':documento' => $documento, ':id' => $id]);
        if ($stmt_check->fetch()) {
            echo "<script>alert('Erro: Já existe outra pessoa com este nome ou documento.'); window.history.back();</script>";
            exit();
        }
        $sql = "UPDATE pessoas SET nome = :nome, is_transportadora = :is_transportadora, documento = :documento, empresa = :empresa, telefone = :telefone WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id, ':nome' => $nome, ':is_transportadora' => $is_transportadora, ':documento' => $documento, ':empresa' => $empresa, ':telefone' => $telefone]);
        header("Location: cadastro-pessoa.php");
        exit();
    } catch (PDOException $e) {
        die("Erro ao atualizar os dados da pessoa: " . $e->getMessage());
    }
} else {
    header("Location: cadastro-pessoa.php");
    exit();
}
?>