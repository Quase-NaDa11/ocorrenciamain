<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografar senha

    // Verificar se o email já está cadastrado
    $query = "SELECT id FROM Coordenador WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Este email já está cadastrado!";
    } else {
        // Inserir no banco de dados - sem campo admin
        $query = "INSERT INTO Coordenador (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $nome, $email, $senha);

        if ($stmt->execute()) {
            echo "Cadastro realizado com sucesso!";
            header("Location: /ocorrenciamain/public/geral.html"); // Redirecionar para login
            exit();
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }
    }
}
?>
