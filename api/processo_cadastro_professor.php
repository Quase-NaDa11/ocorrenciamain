<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $disciplinas = $_POST['disciplinas'] ?? '';
    $dt = isset($_POST['dt']) ? 1 : 0;
    $turma_dt = $_POST['turma_dt'] ?? null;
    $senha = $_POST['senha'] ?? '';

    // Validação simples
    if (empty($nome) || empty($email) || empty($cpf) || empty($senha)) {
        die('Por favor, preencha todos os campos obrigatórios.');
    }

    if ($dt == 1 && empty($turma_dt)) {
        die('Selecione a turma para o Diretor de Turma.');
    }

    // Verifica se já existe professor com o mesmo email
    $sql_check = "SELECT id FROM Professor WHERE email_institucional = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        die('Já existe um professor com esse email institucional.');
    }
    $stmt_check->close();

    // Criptografa a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserção no banco
    $sql = "INSERT INTO Professor (nome, email_institucional, cpf, disciplinas, dt, turma_dt, senha) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $nome, $email, $cpf, $disciplinas, $dt, $turma_dt, $senha_hash);

    if ($stmt->execute()) {
        echo "Professor cadastrado com sucesso!";
        // Pode redirecionar para a página de login
        header("Location: /ocorrenciamain/public/geral.html");
        exit();
    } else {
        echo "Erro ao cadastrar professor: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
