<?php
session_start();
include 'conexao.php';

// Verifica se aluno está logado
if (!isset($_SESSION['aluno_nome'])) {
    header("Location: /ocorrenciamain/public/login_aluno.php"); 
    exit();
}

$nomeAluno = $_SESSION['aluno_nome'];

// Consulta ocorrências do aluno logado
$sql = "SELECT
          Ocorrencia.id,
          Ocorrencia.estudante,
          Ocorrencia.situacao,
          Ocorrencia.data,
          Professor.nome AS professor,
          Coordenador.nome AS coordenador,
          Ocorrencia.status,
          Ocorrencia.lida
        FROM Ocorrencia
        LEFT JOIN Professor ON Ocorrencia.professor_id = Professor.id
        LEFT JOIN Coordenador ON Ocorrencia.coordenador_id = Coordenador.id
        WHERE Ocorrencia.estudante = ?
        ORDER BY Ocorrencia.data DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nomeAluno);
$stmt->execute();
$result = $stmt->get_result();

$notificacoes = [];
$qtdPendentesNaoLidas = 0;

while ($row = $result->fetch_assoc()) {
    $notificacoes[] = $row;
    if ($row['status'] === 'pendente' && $row['lida'] == 0) {
        $qtdPendentesNaoLidas++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Histórico de Ocorrências - Aluno</title>
<link rel="stylesheet" href="/ocorrenciamain/public/historico_aluno.css" />
<style>
  main {
    background: white;
    padding: 30px;
    border-radius: 6px;
    max-width: 1100px;
    margin: 20px auto;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    position: relative;
  }

  #filtros button {
    margin-right: 10px;
  }

  .table {
    margin-top: 10px;
  }

  /* Sino */
  #notificacao {
    position: absolute;
    top: 20px;
    right: 20px;
    cursor: pointer;
    width: 32px;
    height: 32px;
    z-index: 10;
    fill: green;
  }

  #notificacao span.badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #e03e3e;
    color: white;
    font-size: 13px;
    border-radius: 50%;
    padding: 3px 7px;
    font-weight: 700;
    box-shadow: 0 0 3px rgba(0,0,0,0.3);
  }

  /* Lista notificações */
  #lista-notificacoes {
    display: none;
    position: absolute;
    right: 0;
    top: 40px;
    background: white;
    border: 1px solid #ccc;
    width: 340px;
    max-height: 260px;
    overflow-y: auto;
    box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    border-radius: 6px;
    z-index: 1000;
  }

  #lista-notificacoes ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  #lista-notificacoes li {
    border-bottom: 1px solid #eee;
    padding: 12px 14px;
    font-size: 14px;
    color: #333;
    cursor: pointer;
    background: #f0f0f0; /* cinza para não lido */
  }

  #lista-notificacoes li.lida {
    background: white;
    color: #666;
  }

  #lista-notificacoes li:last-child {
    border-bottom: none;
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

<main>

  <!-- Sino no canto superior direito do main -->
  <div id="notificacao" title="Notificações">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false" style="width: 100%; height: 100%;">
      <path d="M12 2a7 7 0 0 0-7 7v4.586l-1.707 1.707A1 1 0 0 0 4 17h16a1 1 0 0 0 .707-1.707L19 13.586V9a7 7 0 0 0-7-7Zm0 19a3 3 0 0 0 3-3H9a3 3 0 0 0 3 3Z"/>
    </svg>
    <?php if ($qtdPendentesNaoLidas > 0): ?>
      <span class="badge" id="badge-count"><?= $qtdPendentesNaoLidas ?></span>
    <?php endif; ?>
    <div id="lista-notificacoes">
      <ul>
        <?php if (empty($notificacoes)): ?>
          <li>Nenhuma notificação</li>
        <?php else: ?>
          <?php foreach ($notificacoes as $n): ?>
            <?php 
              $classeLida = ($n['lida'] == 1) ? 'lida' : '';
              $dataFormatada = date('d/m/Y H:i', strtotime($n['data']));
              $situacao = htmlspecialchars($n['situacao']);
              $status = htmlspecialchars($n['status']);
            ?>
            <li class="<?= $classeLida ?>" data-id="<?= $n['id'] ?>">
              <strong><?= $situacao ?></strong><br/>
              <small><?= $dataFormatada ?> - Status: <?= $status ?></small>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <!-- Botões filtro -->
  <div id="filtros" style="margin-top: 70px; margin-bottom: 20px;">
    <button type="button" id="concluido" class="btn btn-success btn-xs">Concluído</button>
    <button type="button" id="pendente" class="btn btn-warning btn-xs">Pendente</button>
    <button type="button" id="todos" class="btn btn-xs">Todos</button>
  </div>

  <!-- Tabela de ocorrências -->
  <div class="table">
    <table>
      <thead>
        <tr>
          <th>Nome</th>
          <th>Problema</th>
          <th>Data</th>
          <th>Professor</th>
          <th>Coordenador</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="table-body">
        <?php foreach ($notificacoes as $row): ?>
          <tr data-status="<?= htmlspecialchars($row['status']) ?>">
            <td data-label="Nome"><?= htmlspecialchars($row['estudante']) ?></td>
            <td data-label="Problema"><?= htmlspecialchars($row['situacao']) ?></td>
            <td data-label="Data"><?= date('d/m/Y H:i', strtotime($row['data'])) ?></td>
            <td data-label="Professor"><?= $row['professor'] ? htmlspecialchars($row['professor']) : '-' ?></td>
            <td data-label="Coordenador"><?= $row['coordenador'] ? htmlspecialchars($row['coordenador']) : '-' ?></td>
            <td data-label="Status"><?= htmlspecialchars($row['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</main>

<script>
  // Filtro por status
  document.getElementById("concluido").addEventListener("click", () => filterTable("concluido"));
  document.getElementById("pendente").addEventListener("click", () => filterTable("pendente"));
  document.getElementById("todos").addEventListener("click", () => filterTable("todos"));

  function filterTable(status) {
    let rows = document.querySelectorAll("#table-body tr");
    rows.forEach(row => {
      if (status === "todos" || row.getAttribute("data-status") === status) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  }

  // Toggle lista notificações
  const sino = document.getElementById('notificacao');
  const lista = document.getElementById('lista-notificacoes');
  const badgeCount = document.getElementById('badge-count');

  sino.addEventListener('click', () => {
    lista.style.display = (lista.style.display === 'block') ? 'none' : 'block';
  });

  // Fecha lista se clicar fora
  document.addEventListener('click', (e) => {
    if (!sino.contains(e.target) && !lista.contains(e.target)) {
      lista.style.display = 'none';
    }
  });

  // Marca notificacao como lida via AJAX
  document.querySelectorAll('#lista-notificacoes li[data-id]').forEach(item => {
    item.addEventListener('click', () => {
      if (item.classList.contains('lida')) return; // já lida, não faz nada

      const id = item.getAttribute('data-id');

      fetch('/ocorrenciamain/api/marcar_lida.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          item.classList.add('lida');
          item.style.background = 'white';
          item.style.color = '#666';

          // Atualiza badge
          if (badgeCount) {
            let count = parseInt(badgeCount.textContent);
            count = count > 1 ? count - 1 : 0;
            if (count === 0) {
              badgeCount.style.display = 'none';
            } else {
              badgeCount.textContent = count;
            }
          }
        } else {
          alert('Erro ao marcar como lida');
        }
      });
    });
  });
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
