<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ocorrencias";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"] ?? '';
    $email = $_POST["email"] ?? '';
    $cpf = $_POST["cpf"] ?? '';
    $disciplinas = $_POST["disciplinas"] ?? '';
    $senha = $_POST["senha"] ?? '';

    if (empty($nome) || empty($email) || empty($cpf) || empty($disciplinas) || empty($senha)) {
        die("Erro: Todos os campos são obrigatórios.");
    }

    // Verifica duplicidade de e-mail
    $sql_verifica = "SELECT id FROM professor WHERE email_institucional = ?";
    $stmt = $conn->prepare($sql_verifica);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Erro: Este email já está cadastrado.");
    }
    $stmt->close();

    // Criptografar senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir professor
    $sql = "INSERT INTO professor (nome, email_institucional, cpf, disciplina, senha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome, $email, $cpf, $disciplinas, $senha_hash);

    if ($stmt->execute()) {
        header("Location: /ocorrenciamain/public/geral.html");
        exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
