<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = ""; // Ou coloque a senha se for diferente
$dbname = "ocorrencias"; // Substitua pelo nome correto do seu banco de dados

// Criação de conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificação de erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o ID foi passado
if (isset($_POST['id'])) {
    $professor_id = $_POST['id'];

    // Consulta para excluir o professor
    $sql = "DELETE FROM professores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $professor_id);

    if ($stmt->execute()) {
        echo "Professor excluído com sucesso!";
    } else {
        echo "Erro ao excluir professor: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID do professor não especificado.";
}

$conn->close();
?>
