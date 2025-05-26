<?php
include 'conexao.php';

// Buscar ocorrências que ainda não foram recebidas pela coordenação
$query = "SELECT o.id, o.estudante, o.aula, o.situacao, o.turma, p.nome AS professor, o.data, o.descricao 
          FROM Ocorrencia o
          JOIN Professor p ON o.professor_id = p.id
          LEFT JOIN Coordenacao c ON o.id = c.ocorrencia_id
          WHERE c.id IS NULL";

$result = $conn->query($query);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recebimento de Ocorrências</title>
    <link rel="stylesheet" href="/ocorrenciamain/public/historico2.css">
</head>
<body>

<header>
    <div class="logo-container">
        <img src="/ocorrenciamain/img/brasao-do-ceara.png" alt="Brasão do Ceará" width="70px">
        <p class="texto">
            GOVERNO DO ESTADO DO CEARÁ <br>
            19ª COORDENADORIA REGIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO <br>
            ESCOLA ESTADUAL DE EDUCAÇÃO PROFISSIONAL PAULO BARBOSA LEITE
        </p>
        <img src="/ocorrenciamain/img/escola-removebg-preview.png" alt="Logo da Escola" width="100px">
    </div>
</header>

<main class="main-container">
    <a href="/ocorrenciamain/public/geral.html" class="btn-voltar">Voltar</a>
    
    <h2>Recebimento de Ocorrências</h2>

    <?php if ($result->num_rows > 0) { ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Estudante</th>
                        <th>Aula</th>
                        <th>Problema</th>
                        <th>Turma</th>
                        <th>Professor</th>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Data de Recebimento</th>
                        <th>Responsável</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['estudante']); ?></td>
                        <td><?php echo htmlspecialchars($row['aula']); ?></td>
                        <td><?php echo htmlspecialchars($row['situacao']); ?></td>
                        <td><?php echo htmlspecialchars($row['turma']); ?></td>
                        <td><?php echo htmlspecialchars($row['professor']); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($row['data'])); ?></td>
                        <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                        <td>
                            <form action="registrar_recebimento.php" method="post">
                                <input type="hidden" name="ocorrencia_id" value="<?php echo $row['id']; ?>">
                                <input type="date" name="data_recebimento" required>
                        </td>
                        <td>
                            <input type="text" name="responsavel" required placeholder="Nome do responsável">
                        </td>
                        <td>
                            <button type="submit">Registrar</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="mensagem-vazia">Não há ocorrências pendentes para recebimento.</p>
    <?php } ?>
</main>

</body>
</html>
