<?php
session_start();
include 'conexao.php';

// Verifica se o coordenador está logado
if (!isset($_SESSION['coordenacao_id'])) {
    die("Erro: usuário não está logado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $estudante = $_POST['estudante'] ?? '';
    $aula = $_POST['aula'] ?? '';
    $situacao = $_POST['situacao'] ?? ($_POST['situacao_outros'] ?? '');
    $turma = $_POST['turma'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $encaminhamento = $_POST['encaminhamento'] ?? '';
    $data = $_POST['data'] ?? '';

    // Verifica se os campos obrigatórios estão preenchidos
    if (empty($estudante) || empty($aula) || empty($situacao) || empty($turma) || empty($descricao) || empty($data)) {
        die("Erro: Todos os campos obrigatórios devem ser preenchidos.");
    }

    // Pega o ID do coordenador da sessão
    $coordenador_id = $_SESSION['coordenacao_id'];

    // Converte a data para o formato do MySQL
    $data_sql = date('Y-m-d', strtotime($data));

    // Define os campos padrão: professor_id será NULL e status será "pendente"
    $sql = "INSERT INTO Ocorrencia (estudante, aula, situacao, turma, professor_id, coordenador_id, data, descricao, encaminhamento, status)
            VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, 'pendente')";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("ssssisss", $estudante, $aula, $situacao, $turma, $coordenador_id, $data_sql, $descricao, $encaminhamento);

    if ($stmt->execute()) {
        header("Location: /ocorrenciamain/api/historico_coordenador.php");
        exit();
    } else {
        echo "Erro ao cadastrar ocorrência: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    die("Método inválido.");
}
?>
