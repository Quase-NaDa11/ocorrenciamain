<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verifica se os campos foram preenchidos
    if (empty($_POST['login']) || empty($_POST['senha'])) {
        echo "⚠️ Por favor, preencha todos os campos.";
        exit();
    }

    $login = trim($_POST['login']);
    $senha = $_POST['senha'];

    // Verifica conexão
    if ($conn->connect_error) {
        die("Erro de conexão com o banco de dados: " . $conn->connect_error);
    }

    // Consulta por email OU matrícula
    $sql = "SELECT id, nome, senha FROM aluno WHERE email = ? OR matricula = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Erro na preparação da consulta: " . $conn->error;
        exit();
    }

    $stmt->bind_param("ss", $login, $login);

    if (!$stmt->execute()) {
        echo "Erro ao executar consulta: " . $stmt->error;
        exit();
    }

    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nome, $senha_hash);
        $stmt->fetch();

        // Verifica a senha com password_verify
        if (password_verify($senha, $senha_hash)) {
            $_SESSION['aluno_id'] = $id;
            $_SESSION['aluno_nome'] = $nome;

            // Redireciona para o histórico do aluno
            header("Location: historico_aluno.php");
            exit();
        } else {
            echo " Senha incorreta. Verifique e tente novamente.";
        }
    } else {
        echo " Aluno não encontrado com esse email ou matrícula.";
    }

    $stmt->close();
} else {
    echo " Acesso inválido ao script.";
}

$conn->close();
?>
