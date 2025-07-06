<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        die("Por favor, preencha email e senha.");
    }

    // Buscando dados do professor incluindo dt e turma_dt
    $sql = "SELECT id, nome, senha, dt, turma_dt FROM Professor WHERE email_institucional = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $nome, $senha_hash, $dt, $turma_dt);
        $stmt->fetch();

        if (password_verify($senha, $senha_hash)) {
            $_SESSION['id'] = $id;
            $_SESSION['nome'] = $nome;
            $_SESSION['dt'] = $dt;                 // 1 se Diretor de Turma, 0 se não
            $_SESSION['turma_dt'] = $turma_dt;     // turma do diretor

            header("Location: /ocorrenciamain/api/historico_professor.php");
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
