<?php
$host = "localhost";  // Servidor do MySQL (padrão: localhost)
$usuario = "root";    // Usuário padrão do MySQL no XAMPP
$senha = "";          // Senha padrão (vazia no XAMPP)
$banco = "ocorrencias"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
