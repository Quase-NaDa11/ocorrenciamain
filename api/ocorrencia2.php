<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['coordenador_id']) || !isset($_SESSION['nome'])) {
    die("Erro: usuário não está logado.");
}

$estudante = $_POST['estudante'] ?? '';
$aula = $_POST['aula'] ?? '';
$situacao = $_POST['situacao'] ?? '';
if ($situacao === 'outros' && !empty($_POST['situacao_outros'])) {
    $situacao = $_POST['situacao_outros'];
}
$turma = $_POST['turma'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$encaminhamento = $_POST['encaminhamento'] ?? '';
$data = $_POST['data'] ?? '';
$coordenador_id = $_SESSION['coordenador_id'];

if (empty($estudante) || empty($aula) || empty($situacao) || empty($turma) || empty($descricao) || empty($data)) {
    die("Erro: todos os campos obrigatórios devem ser preenchidos.");
}

// Corrige o formato da data para MySQL DATETIME
$data = str_replace("T", " ", $data) . ":00";

$query = "INSERT INTO Ocorrencia (estudante, aula, situacao, turma, descricao, encaminhamento, data, coordenador_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Erro na preparação da query: " . $conn->error);
}

$stmt->bind_param("sisssssi", $estudante, $aula, $situacao, $turma, $descricao, $encaminhamento, $data, $coordenador_id);

if ($stmt->execute()) {
    header("Location: /ocorrenciamain/api/historico_coordenador.php");
    exit;
} else {
    die("Erro ao salvar ocorrência: " . $stmt->error);
}
?>
