<?php
include 'verificar_sessao.php';
include 'conexao.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $stmt_check = $pdo->prepare("SELECT nome, documento FROM pessoas WHERE nome = :nome OR documento = :documento");
        $stmt_check->execute([':nome' => $nome, ':documento' => $documento]);
        $existente = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($existente) {
            $mensagem_erro = "Erro: ";
            if ($existente['nome'] == $nome) { $mensagem_erro .= "O nome '" . htmlspecialchars($nome) . "' já está cadastrado."; } 
            else { $mensagem_erro .= "O documento '" . htmlspecialchars($documento) . "' já está cadastrado."; }
            echo "<script>alert('" . addslashes($mensagem_erro) . "'); window.history.back();</script>";
            exit();
        }
        $sql = "INSERT INTO pessoas (nome, is_transportadora, documento, empresa, telefone) VALUES (:nome, :is_transportadora, :documento, :empresa, :telefone)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nome' => $nome, ':is_transportadora' => $is_transportadora, ':documento' => $documento, ':empresa' => $empresa, ':telefone' => $telefone]);
        echo "<script>alert('Pessoa cadastrada com sucesso!'); window.location.href='cadastro-pessoa.php';</script>";
    } catch (PDOException $e) {
        die("Erro ao cadastrar pessoa: " . $e->getMessage());
    }
}
?>