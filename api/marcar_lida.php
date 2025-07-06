<?php
session_start();
include 'conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id'])) {
  echo json_encode(['success' => false, 'message' => 'ID não informado']);
  exit;
}

$id = intval($data['id']);

if (!isset($_SESSION['aluno_nome'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}
$nomeAluno = $_SESSION['aluno_nome'];

$stmt = $conn->prepare("UPDATE Ocorrencia SET lida = 1 WHERE id = ? AND estudante = ?");
$stmt->bind_param("is", $id, $nomeAluno);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Erro no banco']);
}

$stmt->close();
$conn->close();
?>
