<?php
include 'conexao.php';

// Verifica se um ID foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do professor não fornecido.");
}

$id = $_GET['id'];

// Busca os dados do professor pelo ID
$query = "SELECT * FROM Professor WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();

// Se o professor não for encontrado
if (!$professor) {
    die("Professor não encontrado.");
}

// Processa o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email_institucional'];
    $cpf = $_POST['cpf'];
    $disciplina = $_POST['disciplina'];

    // Atualiza os dados no banco
    $updateQuery = "UPDATE Professor SET nome = ?, email_institucional = ?, cpf = ?, disciplina = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $nome, $email, $cpf, $disciplina, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Dados atualizados com sucesso!'); window.location.href='listar_professores.php';</script>";
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
    <title>Editar Professor</title>
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
        <h1>Editar Professor</h1>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($professor['nome']); ?>" required>

            <label for="email">Email Institucional:</label>
            <input type="email" id="email" name="email_institucional" value="<?php echo htmlspecialchars($professor['email_institucional']); ?>" required>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($professor['cpf']); ?>" required>

            <label for="disciplina">Disciplina:</label>
            <input type="text" id="disciplina" name="disciplina" value="<?php echo htmlspecialchars($professor['disciplina']); ?>" required>

            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>
