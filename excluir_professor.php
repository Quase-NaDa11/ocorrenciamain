<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ocorrencias";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $professor_id = 1; // Exemplo, você pode pegar o ID de forma dinâmica

    // Consulta para excluir o professor
    $sql = "DELETE FROM professor WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $professor_id);

    if ($stmt->execute()) {
        echo "Professor excluído com sucesso!";
    } else {
        echo "Erro ao excluir professor: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
