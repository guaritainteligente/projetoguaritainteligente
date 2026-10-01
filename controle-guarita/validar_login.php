<?php
session_start();
include 'conexao.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha_digitada = $_POST['senha'];
    try {
        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($senha_digitada, $user['senha'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['usuario'];
            header("Location: index.php");
            exit();
        } else {
            header("Location: login.php?erro=1");
            exit();
        }
    } catch (PDOException $e) {
        die("Erro ao tentar fazer login: ". $e->getMessage());
    }
} else {
    header("Location: login.php");
    exit();
}
?>