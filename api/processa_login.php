<?php
session_start();
include 'conexao.php'; // Arquivo de conexão com o banco

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepara a consulta SQL
    $sql = "SELECT id, nome, senha FROM Professor WHERE email_institucional = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $row['senha'])) {
            $_SESSION['id_professor'] = $row['id'];
            $_SESSION['nome_professor'] = $row['nome'];
            header("Location: historico_professor.php"); // Redireciona após login
            exit();
        } else {
            echo "<script>alert('Senha incorreta!'); window.location.href='/ocorrenciamain/public/login_professor.html';</script>";
        }
    } else {
        echo "<script>alert('E-mail não encontrado!'); window.location.href='/ocorrenciamain/public/login_professor.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
