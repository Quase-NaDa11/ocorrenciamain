<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ocorrencias"; // Altere para o nome do seu banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica se os campos do formulário foram preenchidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"] ?? '';
    $email = $_POST["email"] ?? '';
    $cpf = $_POST["cpf"] ?? '';
    $disciplinasContainer = $_POST["disciplina"] ?? '';
    $senha = $_POST["senha"] ?? '';

    // Verificar se todos os campos foram preenchidos
    if (empty($nome) || empty($email) || empty($cpf) || empty($disciplinasContainer) || empty($senha)) {
        die("Erro: Todos os campos são obrigatórios.");
    }

    // Verificar se o email já existe no banco de dados
    $sql_verifica = "SELECT id FROM professor WHERE email_institucional = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $email);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        die("Erro: Este email já está cadastrado.");
    }
    $stmt_verifica->close();

    // Hash da senha antes de salvar no banco de dados
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir dados na tabela "professor"
    $sql = "INSERT INTO professor (nome, email_institucional, cpf, disciplina, senha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome, $email, $cpf, $disciplina, $senha_hash);

    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso!";
        header("Location: /ocorrenciamain/public/geral.html"); // Redirecionar para login
        exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
