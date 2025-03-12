<?php
include 'conexao.php'; // Arquivo de conexão com o banco

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pegando os dados do formulário
    $estudante = $_POST['estudante'];
    $aula = $_POST['aula'];
    $situacao = $_POST['situacao'];
    $turma = $_POST['turma'];
    $professor_nome = $_POST['professor_nome']; // Nome do professor
    $data = $_POST['data'];
    $descricao = $_POST['descricao'];
    $encaminhamento = $_POST['encaminhamento'];

    // Verificar se os campos estão preenchidos
    if (empty($estudante) || empty($aula) || empty($situacao) || empty($turma) || empty($professor_nome) || empty($data) || empty($descricao)) {
        die("Erro: Todos os campos obrigatórios devem ser preenchidos.");
    }

    // Buscar o ID do professor pelo nome
    $sql_professor = "SELECT id FROM Professor WHERE nome = ?";
    $stmt_professor = $conn->prepare($sql_professor);
    $stmt_professor->bind_param("s", $professor_nome);
    $stmt_professor->execute();
    $stmt_professor->store_result();

    // Se encontrou o professor
    if ($stmt_professor->num_rows > 0) {
        $stmt_professor->bind_result($professor_id);
        $stmt_professor->fetch();
        $stmt_professor->close();

        // Inserir ocorrência com o professor_id
        $sql = "INSERT INTO Ocorrencia (estudante, aula, situacao, turma, professor_id, data, descricao, encaminhamento) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisss", $estudante, $aula, $situacao, $turma, $professor_id, $data, $descricao, $encaminhamento);

        if ($stmt->execute()) {
           header("Location: historico.php");
           exit();
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro: Professor não encontrado!";
    }

    $conn->close();
}
?>
