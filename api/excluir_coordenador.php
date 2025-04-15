<?php
include 'conexao.php'; // Conexão com o banco de dados

// Verifica se o ID foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do coordenador não fornecido.");
}

$id = $_GET['id'];

// Exclui o coordenador do banco de dados
$query = "DELETE FROM Coordenador WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Coordenador excluído com sucesso!'); window.location.href='listar_coordenadores.php';</script>";
} else {
    echo "Erro ao excluir coordenador.";
}
?>
