<?php
include 'conexao.php'; // Conexão com o banco de dados

// Consulta para buscar todos os professores
$query = "SELECT id, nome, email_institucional, cpf, disciplina FROM Professor";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Professores</title>
    <link rel="stylesheet" href="/ocorrenciamain/public/listar2.css">
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
    <a href="/ocorrenciamain/public/geral.html" class="btn-voltar">Voltar</a>
        <h1>Lista de Professores</h1>
        <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email Institucional</th>
                    <th>CPF</th>
                    <th>Disciplina</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                        <td><?php echo htmlspecialchars($row['email_institucional']); ?></td>
                        <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($row['disciplina']); ?></td>
                        <td>
                            <a href="editar_professor.php?id=<?php echo $row['id']; ?>" class="button edit-button">Editar</a>
                            <a href="excluir_professor.php?id=<?php echo $row['id']; ?>" class="button delete-button" onclick="return confirm('Tem certeza que deseja excluir este professor?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
