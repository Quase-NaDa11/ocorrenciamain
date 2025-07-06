<?php
include 'conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: listar_alunos_por_turma.php');
    exit;
}

$id = intval($_GET['id']);

// Opcional: pegar turma para redirecionar após exclusão
$stmt = $conn->prepare("SELECT turma FROM aluno WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Aluno não encontrado.";
    exit;
}

$aluno = $result->fetch_assoc();
$stmt->close();

// Deletar aluno
$stmt = $conn->prepare("DELETE FROM aluno WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    // Redirecionar para lista da turma dele
    header('Location: listar_alunos_por_turma.php?turma=' . urlencode($aluno['turma']));
    exit;
} else {
    echo "Erro ao excluir aluno: " . $conn->error;
}
