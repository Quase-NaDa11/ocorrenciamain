<?php
session_start();
include 'conexao.php'; // Arquivo de conexão com o banco

if (!isset($_SESSION['coordenacao_id'])) {
    die("Erro: usuário não está logado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegando os dados do formulário
    $estudante = $_POST['estudante'];
    $aula = $_POST['aula'];
    $situacao = $_POST['situacao'];
    $turma = $_POST['turma'];
    $coordenador_id = $_SESSION['coordenacao_id']; // id do coordenador da sessão
    $data = $_POST['data'];
    $descricao = $_POST['descricao'];
    $encaminhamento = $_POST['encaminhamento'];

    // Verificar se os campos estão preenchidos
    if (empty($estudante) || empty($aula) || empty($situacao) || empty($turma) || empty($data) || empty($descricao)) {
        die("Erro: Todos os campos obrigatórios devem ser preenchidos.");
    }

    // Inserir ocorrência com o coordenador_id
    $sql = "INSERT INTO Ocorrencia (estudante, aula, situacao, turma, coordenador_id, data, descricao, encaminhamento) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisss", $estudante, $aula, $situacao, $turma, $coordenador_id, $data, $descricao, $encaminhamento);

    if ($stmt->execute()) {
       header("Location: historico_coordenador.php");
       exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
