<?php
include 'verificar_sessao.php';
include 'conexao.php';
if (isset($_GET['tabela']) && isset($_GET['id'])) {
    $tabela = $_GET['tabela'];
    $id = $_GET['id'];
    $tabelas_permitidas = ['pessoas', 'veiculos'];
    if (in_array($tabela, $tabelas_permitidas)) {
        try {
            $sql = "UPDATE $tabela SET ativo = 0 WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $pagina_de_retorno = 'cadastro-';
            if ($tabela === 'pessoas') { $pagina_de_retorno .= 'pessoa'; } 
            elseif ($tabela === 'veiculos') { $pagina_de_retorno .= 'veiculo'; }
            header("Location: " . $pagina_de_retorno . ".php");
            exit();
        } catch (PDOException $e) {
            die("Erro ao desativar o registro: " . $e->getMessage());
        }
    } else {
        die("Operação não permitida: tabela inválida.");
    }
} else {
    die("Parâmetros inválidos.");
}
?>