<?php
include 'conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: listar_alunos_por_turma.php');
    exit;
}

$id = intval($_GET['id']);

// Buscar dados do aluno para preencher o formulário
$stmt = $conn->prepare("SELECT nome, email, matricula, cpf, turma FROM aluno WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Aluno não encontrado.";
    exit;
}

$aluno = $result->fetch_assoc();
$stmt->close();

// Se o formulário for enviado para salvar alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $matricula = $_POST['matricula'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $turma = $_POST['turma'] ?? '';

    // Validar campos obrigatórios (básico)
    if (!$nome || !$email || !$matricula || !$cpf || !$turma) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // Atualizar no banco
        $stmt = $conn->prepare("UPDATE aluno SET nome=?, email=?, matricula=?, cpf=?, turma=? WHERE id=?");
        $stmt->bind_param("sssssi", $nome, $email, $matricula, $cpf, $turma, $id);

        if ($stmt->execute()) {
            header('Location: listar_alunos_por_turma.php?turma=' . urlencode($turma));
            exit;
        } else {
            $erro = "Erro ao atualizar aluno: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Editar Aluno</title>
<link rel="stylesheet" href="/ocorrenciamain/public/editar.css">
<script>
  function formatarCPF(campo) {
  let cpf = campo.value;

  // Remove tudo que não for número
  cpf = cpf.replace(/\D/g, "");

  // Limita a 11 dígitos
  if (cpf.length > 11) {
    cpf = cpf.slice(0, 11);
  }

  // Aplica a formatação somente se o usuário está digitando (com base no tamanho)
  if (cpf.length > 0) {
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
  }
  if (cpf.length > 3) {
    cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
  }
  if (cpf.length > 6) {
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
  }

  campo.value = cpf;
}

</script>
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
      <a href="listar_alunos_por_turma.php" class="btn-voltar">Voltar</a>
    <h1>Editar Aluno</h1>

    <?php if (!empty($erro)): ?>
        <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($aluno['email']); ?>" required>

        <label for="matricula">Matrícula:</label>
        <input type="text" name="matricula" id="matricula" value="<?php echo htmlspecialchars($aluno['matricula']); ?>" required>

       
            <label>CPF:</label>
          <input 
  type="text" 
  name="cpf" 
  id="cpf" 
  maxlength="14" 
  placeholder="000.000.000-00" 
  required 
  onkeyup="formatarCPF(this)" 
  value="<?php echo htmlspecialchars($aluno['cpf']); ?>"
>
        <label for="turma">Turma:</label>
        <input type="text" name="turma" id="turma" value="<?php echo htmlspecialchars($aluno['turma']); ?>" required>

        <button type="submit">Salvar Alterações</button>
    </form>

    
</div>
</body>
</html>
