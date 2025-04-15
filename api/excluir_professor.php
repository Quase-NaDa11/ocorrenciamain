<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "DELETE FROM Professor WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Professor excluído com sucesso!'); window.location.href='listar_professores.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir professor!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('ID inválido!'); window.location.href='listar_professores.php';</script>";
}
?>
