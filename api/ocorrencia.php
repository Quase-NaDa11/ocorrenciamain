<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estudante = $_POST['estudante'];
    $aula = $_POST['aula'];
    $situacao = $_POST['situacao'] ?? $_POST['situacao_outros'] ?? '';
    $turma = $_POST['turma'];
    $descricao = $_POST['descricao'];
    $encaminhamento = $_POST['encaminhamento'] ?? '';

    $professor_nome = $_SESSION['nome'] ?? null;
    if (!$professor_nome) {
        die("Erro: professor não logado.");
    }

    date_default_timezone_set('America/Fortaleza');
    $data = date('Y-m-d H:i:s');

    if (empty($estudante) || empty($aula) || empty($situacao) || empty($turma) || empty($descricao)) {
        die("Erro: Todos os campos obrigatórios devem ser preenchidos.");
    }

    // Buscar id do professor
    $sql_professor = "SELECT id FROM Professor WHERE nome = ?";
    $stmt_professor = $conn->prepare($sql_professor);
    $stmt_professor->bind_param("s", $professor_nome);
    $stmt_professor->execute();
    $stmt_professor->store_result();

    if ($stmt_professor->num_rows > 0) {
        $stmt_professor->bind_result($professor_id);
        $stmt_professor->fetch();
        $stmt_professor->close();

        // Inserir ocorrência
        $sql = "INSERT INTO Ocorrencia (estudante, aula, situacao, turma, professor_id, data, descricao, encaminhamento) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisss", $estudante, $aula, $situacao, $turma, $professor_id, $data, $descricao, $encaminhamento);

        if ($stmt->execute()) {
            header("Location: /ocorrenciamain/api/historico_professor.php");
            exit();
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro: Professor não encontrado.";
    }

    $conn->close();
} else {
    echo "Método inválido.";
}
?>
