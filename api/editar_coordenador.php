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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Coordenador</title>
    <link rel="stylesheet" href="/ocorrenciamain/public/editar.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="/ocorrenciamain/img/brasao-do-ceara.png" alt="Brasão do Ceará" width="70px" />
            <p class="texto" style="color: white">
                GOVERNO DO ESTADO DO CEARÁ <br />
                19ª COORDENADORIA REGIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO <br />
                ESCOLA ESTADUAL DE EDUCAÇÃO PROFISSIONAL PAULO BARBOSA LEITE
            </p>
            <img src="/ocorrenciamain/img/escola-removebg-preview.png" alt="Logo da Escola" width="100px" />
        </div>
    </header>

    <div class="container">
        <h1>Editar Coordenador</h1>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($coordenador['nome']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($coordenador['email']); ?>" required>

            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>
