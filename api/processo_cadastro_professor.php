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
    $nome = trim($_POST["nome"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $cpf = trim($_POST["cpf"] ?? '');
    $disciplinas = trim($_POST["disciplinas"] ?? '');
    $senha = $_POST["senha"] ?? '';

    // Validação básica
    if (empty($nome) || empty($email) || empty($cpf) || empty($disciplinas) || empty($senha)) {
        echo "<script>alert('Todos os campos devem ser preenchidos.'); window.history.back();</script>";
        exit();
    }

    // Verifica duplicidade de e-mail
    $sql_verifica = "SELECT id FROM professor WHERE email_institucional = ?";
    $stmt = $conn->prepare($sql_verifica);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Erro: Este e-mail já está cadastrado.'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // Criptografar senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir professor
    $sql = "INSERT INTO professor (nome, email_institucional, cpf, disciplina, senha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome, $email, $cpf, $disciplinas, $senha_hash);

    if ($stmt->execute()) {
        echo "<script>alert('Professor cadastrado com sucesso!'); window.location.href = '/ocorrenciamain/public/geral.html';</script>";
        exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
