<?php
include 'conexao.php'; // Conexão com o banco de dados

// Verifica se o ID foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do coordenador não fornecido.");
}

$id = $_GET['id'];

// Consulta para buscar os dados do coordenador pelo ID
$query = "SELECT * FROM Coordenador WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$coordenador = $result->fetch_assoc();

// Verifica se o coordenador existe
if (!$coordenador) {
    die("Coordenador não encontrado.");
}

// Processa o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Atualiza os dados no banco
    $updateQuery = "UPDATE Coordenador SET nome = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $nome, $email, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Dados atualizados com sucesso!'); window.location.href='listar_coordenadores.php';</script>";
    } else {
        echo "Erro ao atualizar os dados.";
    }
}
?>

