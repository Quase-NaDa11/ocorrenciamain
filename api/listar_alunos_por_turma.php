<?php
// listar_alunos_por_turma.php

include 'conexao.php'; // ajuste o caminho se necessário

// Pega a turma selecionada via GET
$turmaSelecionada = isset($_GET['turma']) ? $_GET['turma'] : '';

// Consulta turmas distintas para popular select
$sqlTurmas = "SELECT DISTINCT turma FROM aluno ORDER BY turma";
$resTurmas = $conn->query($sqlTurmas);

// Consulta alunos da turma selecionada (usando prepared statement)
$alunos = [];
if ($turmaSelecionada) {
    $stmt = $conn->prepare("SELECT id, nome, email, cpf, matricula FROM aluno WHERE turma = ? ORDER BY nome ASC");
    $stmt->bind_param("s", $turmaSelecionada);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $alunos[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Alunos por Turma</title>
<link rel="stylesheet" href="/ocorrenciamain/public/listar.css">

<style>
select {
    padding: 10px 15px;
    font-size: 16px;
    border-radius: 5px;
    background-color: white;
    color: #333;
    cursor: pointer;
    transition: border-color 0.3s ease;
    min-width: 220px;
}
select:hover, select:focus {
    outline: none;
}
.button {
    display: inline-block;
    padding: 6px 14px;
    margin-right: 5px;
    font-size: 14px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    transition: background-color 0.3s ease;
    margin-top: 5px;
}
.container {
    max-width: 900px;
    margin: 20px auto 40px;
    background: white;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 0 12px rgb(0 0 0 / 0.12);
}
</style>
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

    <h1>Alunos por Turma</h1>
    <form method="GET" id="formTurma">
        <label for="turma">Selecione a turma:</label><br />
        <select name="turma" id="turma" onchange="document.getElementById('formTurma').submit()">
            <option value="">-- Selecione --</option>
            <?php while ($turma = $resTurmas->fetch_assoc()) : ?>
                <option value="<?php echo htmlspecialchars($turma['turma']); ?>" <?php if ($turmaSelecionada == $turma['turma']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($turma['turma']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($turmaSelecionada): ?>
        <h2>Alunos da turma <?php echo htmlspecialchars($turmaSelecionada); ?></h2>

        <?php if (count($alunos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>CPF</th>
                        <th>Matrícula</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['cpf']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['matricula']); ?></td>
                        <td>
                            <a href="editar_aluno.php?id=<?php echo $aluno['id']; ?>" class="button edit-button">Editar</a>
                            <a href="excluir_aluno.php?id=<?php echo $aluno['id']; ?>" class="button delete-button" onclick="return confirm('Tem certeza que deseja excluir este aluno?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum aluno encontrado nessa turma.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
