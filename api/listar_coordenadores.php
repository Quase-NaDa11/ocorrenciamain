<?php
include 'conexao.php'; // Conexão com o banco de dados

// Consulta para buscar todos os coordenadores
$query = "SELECT id, nome, email FROM Coordenador";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Coordenadores</title>
    <link rel="stylesheet" href="/ocorrenciamain/public/listar.css">
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
    <a href="/ocorrenciamain/public/geral.html" class="btn btn-warning btn-xs">
    <button>Voltar</button></a>
        <h1>Lista de Coordenadores</h1>
        
                </a>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <a href="editar_coordenador.php?id=<?php echo $row['id']; ?>" class="button edit-button">Alterar</a>
                            <a href="excluir_coordenador.php?id=<?php echo $row['id']; ?>" class="button delete-button" onclick="return confirm('Tem certeza que deseja excluir este coordenador?')">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
