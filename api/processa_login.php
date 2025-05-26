<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        die("Por favor, preencha email e senha.");
    }

    // Troque 'email_institucional' pelo nome correto do campo do seu banco
    $sql = "SELECT id, nome, senha FROM Professor WHERE email_institucional = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $nome, $senha_hash);
        $stmt->fetch();

        // Verifique senha (supondo que está em hash)
        if (password_verify($senha, $senha_hash)) {
            $_SESSION['id'] = $id;
            $_SESSION['nome'] = $nome;

            header("Location: historico_professor.php");
            exit();
        } else {
            echo "Senha incorreta.";
        }
    } else {
        echo "Usuário não encontrado.";
    }
    $stmt->close();
    $conn->close();
}
?>
