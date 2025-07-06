<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $matricula = $_POST['matricula'] ?? '';
    $turma = $_POST['turma'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Validação simples
    if (empty($nome) || empty($email) || empty($cpf) || empty($matricula) || empty($turma) || empty($senha)) {
        die("Todos os campos são obrigatórios.");
    }

    // Verificar se já existe aluno com email, cpf ou matrícula iguais
    $sql_verifica = "SELECT id FROM aluno WHERE email = ? OR cpf = ? OR matricula = ?";
    $stmt = $conn->prepare($sql_verifica);
    $stmt->bind_param("sss", $email, $cpf, $matricula);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Aluno já cadastrado com este email, CPF ou matrícula.");
    }
    $stmt->close();

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir novo aluno com CPF
    $sql = "INSERT INTO aluno (nome, email, cpf, matricula, turma, senha) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nome, $email, $cpf, $matricula, $turma, $senha_hash);

    if ($stmt->execute()) {
        header("Location: /ocorrenciamain/public/geral.html");
        exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
