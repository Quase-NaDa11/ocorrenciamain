<?php
session_start();
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID não informado']);
    exit;
}
$id = intval($data['id']);

if (!isset($_SESSION['diretor_turma_nome'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}
$nomeDiretor = $_SESSION['diretor_turma_nome'];

// Busca turma do diretor para garantir que pode modificar só dessa turma
$sqlTurma = "SELECT turma FROM DiretorTurma WHERE nome = ?";
$stmtTurma = $conn->prepare($sqlTurma);
$stmtTurma->bind_param("s", $nomeDiretor);
$stmtTurma->execute();
$resultTurma = $stmtTurma->get_result();
if ($resultTurma->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Turma não encontrada']);
    exit;
}
$turma = $resultTurma->fetch_assoc()['turma'];
$stmtTurma->close();

// Atualiza só se a ocorrência for da turma do diretor
$sql = "UPDATE Ocorrencia SET lida = 1 WHERE id = ? AND turma = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $turma);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro no banco']);
}

$stmt->close();
$conn->close();
?>
