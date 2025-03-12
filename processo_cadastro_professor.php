<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root"; // Usuário do MySQL (alterar se necessário)
$password = ""; // Senha do MySQL (alterar se necessário)
$dbname = "ocorrencias"; // Nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Capturar dados do formulário
$nome = $_POST['nome'];
$email_institucional = $_POST['email_institucional'];
$cpf = $_POST['cpf'];
$disciplina = $_POST['disciplina'];

// Verificar se o CPF ou o e-mail já existem
$sql_verifica = "SELECT id FROM Professor WHERE cpf = ? OR email_institucional = ?";
$stmt_verifica = $conn->prepare($sql_verifica);
$stmt_verifica->bind_param("ss", $cpf, $email_institucional);
$stmt_verifica->execute();
$stmt_verifica->store_result();

if ($stmt_verifica->num_rows > 0) {
    echo "Erro: CPF ou e-mail já cadastrados!";
} else {
    // Inserir professor no banco de dados
    $sql = "INSERT INTO Professor (nome, email_institucional, cpf, disciplina) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email_institucional, $cpf, $disciplina);

    if ($stmt->execute()) {
        echo "Professor cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
}

$stmt_verifica->close();
$conn->close();
?>
