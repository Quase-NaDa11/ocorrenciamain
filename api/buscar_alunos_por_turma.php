<?php
include 'conexao.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['turma']) || empty($_GET['turma'])) {
    echo json_encode(['erro' => 'Turma não recebida']);
    exit;
}

$turma = trim($_GET['turma']);

// Teste: mostra qual turma chegou

file_put_contents('log_turma.txt', "Recebido: $turma\n", FILE_APPEND);

$stmt = $conn->prepare("SELECT nome FROM aluno WHERE turma = ? ORDER BY nome ASC");

if (!$stmt) {
    echo json_encode(['erro' => 'Erro no prepare: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $turma);

if (!$stmt->execute()) {
    echo json_encode(['erro' => 'Erro na execução: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$alunos = [];

while ($row = $result->fetch_assoc()) {
    $alunos[] = $row['nome'];
}

if (empty($alunos)) {
    echo json_encode(['erro' => "Nenhum aluno encontrado para a turma: $turma"]);
} else {
    echo json_encode($alunos);
}
