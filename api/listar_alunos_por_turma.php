<?php
include 'conexao.php';

$turmaSelecionada = isset($_GET['turma']) ? $_GET['turma'] : '';

$sqlTurmas = "SELECT DISTINCT turma FROM aluno ORDER BY turma";
$resTurmas = $conn->query($sqlTurmas);

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
  <link rel="stylesheet" href="/ocorrenciamain/public/listar_aluno.css" />
</head>
<body>

<header>
  <div class="logo-container">
    <img src="/ocorrenciamain/img/brasao-do-ceara.png" alt="Brasão do Ceará" width="70px" />
    <p class="texto">
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
        <option value="<?= htmlspecialchars($turma['turma']) ?>" <?= $turmaSelecionada == $turma['turma'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($turma['turma']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </form>

  <?php if ($turmaSelecionada): ?>
    <h2>Alunos da turma <?= htmlspecialchars($turmaSelecionada) ?></h2>

    <?php if (count($alunos) > 0): ?>
      <div class="table-container">
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
                <td><?= htmlspecialchars($aluno['nome']) ?></td>
                <td><?= htmlspecialchars($aluno['email']) ?></td>
                <td><?= htmlspecialchars($aluno['cpf']) ?></td>
                <td><?= htmlspecialchars($aluno['matricula']) ?></td>
               <td class="acao">
  <a href="editar_aluno.php?id=<?= $aluno['id']; ?>" class="button edit-button">Editar</a>
  <a href="excluir_aluno.php?id=<?= $aluno['id']; ?>" class="button delete-button" onclick="return confirm('Tem certeza?')">Excluir</a>
</td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>Nenhum aluno encontrado nessa turma.</p>
    <?php endif; ?>
  <?php endif; ?>
</div>

</body>
</html>
