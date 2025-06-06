<?php
session_start();
include 'conexao.php';

// Verifica se coordenador está logado
if (!isset($_SESSION['coordenador_id'])) {
    header("Location: login_coordenador.php");
    exit;
}

$nome_coordenador_logado = $_SESSION['nome'];

// Consulta ocorrências pendentes (que não possuem registro na tabela Coordenacao)
$query = "
SELECT 
    o.id, o.estudante, o.aula, o.situacao, o.turma, o.data, o.descricao, 
    p.nome AS professor
FROM Ocorrencia o
LEFT JOIN Professor p ON o.professor_id = p.id
LEFT JOIN Coordenacao c ON o.id = c.ocorrencia_id
WHERE c.id IS NULL
ORDER BY o.data DESC
";

$result = $conn->query($query);
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Recebimento de Ocorrências</title>
<link rel="stylesheet" href="/ocorrenciamain/public/historico2.css" />
</head>
<body>

<header>
    <div class="logo-container">
        <img src="/ocorrenciamain/img/brasao-do-ceara.png" alt="Brasão do Ceará" width="70" />
        <p class="texto">
            GOVERNO DO ESTADO DO CEARÁ <br />
            19ª COORDENADORIA REGIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO <br />
            ESCOLA ESTADUAL DE EDUCAÇÃO PROFISSIONAL PAULO BARBOSA LEITE
        </p>
        <img src="/ocorrenciamain/img/escola-removebg-preview.png" alt="Logo da Escola" width="100" />
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
                    <td><?= htmlspecialchars($row['estudante']) ?></td>
                    <td><?= htmlspecialchars($row['aula']) ?></td>
                    <td><?= htmlspecialchars($row['situacao']) ?></td>
                    <td><?= htmlspecialchars($row['turma']) ?></td>
                    <td><?= htmlspecialchars($row['professor']) ?></td>
                    <td><?= date("d/m/Y", strtotime($row['data'])) ?></td>
                    <td><?= htmlspecialchars($row['descricao']) ?></td>
                    <td>
                        <form action="registrar_recebimento.php" method="post" style="margin:0;">
                            <input type="hidden" name="ocorrencia_id" value="<?= $row['id'] ?>">
                            <input type="date" name="data_recebimento" required>
                    </td>
                    <td>
                        <input type="text" name="responsavel" value="<?= htmlspecialchars($nome_coordenador_logado) ?>" required readonly style="background:#eee; cursor: not-allowed;">
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
