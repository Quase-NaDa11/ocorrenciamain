<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id'])) {
    header("Location: /ocorrenciamain/public/login_professor.html");
    exit();
}

// Só diretor de turma pode acessar
if (!isset($_SESSION['dt']) || $_SESSION['dt'] != 1) {
    echo "<script>alert('Acesso negado: você não é Diretor de Turma.'); window.location.href = '/ocorrenciamain/api/historico_professor.php';</script>";
    exit();
}

$turmaDT = $_SESSION['turma_dt'];

// Verifica se a coluna 'status' existe e cria se não existir
$verifica_coluna = "SHOW COLUMNS FROM Ocorrencia LIKE 'status'";
$result_coluna = $conn->query($verifica_coluna);
if ($result_coluna->num_rows == 0) {
    $alter_sql = "ALTER TABLE Ocorrencia ADD COLUMN status ENUM('pendente', 'andamento', 'concluido') NOT NULL DEFAULT 'pendente'";
    $conn->query($alter_sql);
}

$sql_ocorrencias = "SELECT
                        Ocorrencia.id,
                        Ocorrencia.estudante,
                        Ocorrencia.situacao,
                        Ocorrencia.data,
                        Professor.nome AS professor,
                        Ocorrencia.status,
                        Ocorrencia.lida
                    FROM Ocorrencia
                    LEFT JOIN Professor ON Ocorrencia.professor_id = Professor.id
                    WHERE Ocorrencia.turma = ?
                    ORDER BY Ocorrencia.data DESC";

$stmt = $conn->prepare($sql_ocorrencias);
$stmt->bind_param("s", $turmaDT);
$stmt->execute();
$result = $stmt->get_result();

$notificacoes = [];
$qtdPendentesNaoLidas = 0;
while ($row = $result->fetch_assoc()) {
    $notificacoes[] = $row;
    if ($row['status'] === 'pendente' && isset($row['lida']) && $row['lida'] == 0) {
        $qtdPendentesNaoLidas++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ocorrências da Minha Turma - Diretor de Turma</title>
<link rel="stylesheet" href="/ocorrenciamain/public/historico.css" />
<style>
  #notificacao {
    position: fixed;
    top: 20px;
    right: 20px;
    cursor: pointer;
    z-index: 10000;
  }
  #badge-count {
    background: red;
    color: white;
    font-weight: bold;
    border-radius: 50%;
    padding: 2px 7px;
    font-size: 14px;
    position: absolute;
    top: -6px;
    right: -6px;
  }
  #lista-notificacoes {
    display: none;
    position: absolute;
    top: 36px;
    right: 0;
    width: 350px;
    max-height: 400px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
  }
  #lista-notificacoes ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  #lista-notificacoes li {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    cursor: pointer;
  }
  #lista-notificacoes li.lida {
    background: #eee;
    color: #888;
  }
  #lista-notificacoes li:hover:not(.lida) {
    background: #f0f8ff;
  }
  #btn-relatorio {
    margin-left: 15px;
    padding: 6px 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
</style>
</head>
<body>
<header>
  <div class="logo-container">
    <img src="/ocorrenciamain/img/brasao-do-ceara.png" width="70px" />
    <p class="texto" style="color: white">GOVERNO DO ESTADO DO CEARÁ <br /> 19ª CREDE - EEEP PAULO BARBOSA LEITE</p>
    <img src="/ocorrenciamain/img/escola-removebg-preview.png" width="100px" />
  </div>
</header>

<main>
  <div class="tudo">
    <!-- Sininho -->
    <div id="notificacao">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: green;">
        <path d="M12 2a7 7 0 0 0-7 7v4.586l-1.707 1.707A1 1 0 0 0 4 17h16a1 1 0 0 0 .707-1.707L19 13.586V9a7 7 0 0 0-7-7Zm0 19a3 3 0 0 0 3-3H9a3 3 0 0 0 3 3Z"/>
      </svg>
      <?php if ($qtdPendentesNaoLidas > 0): ?>
        <span class="badge" id="badge-count"><?= $qtdPendentesNaoLidas ?></span>
      <?php endif; ?>
      <div id="lista-notificacoes">
        <ul>
          <?php foreach ($notificacoes as $n): 
              $classeLida = ($n['lida'] == 1) ? 'lida' : '';
              $data = date('d/m/Y H:i', strtotime($n['data']));
          ?>
            <li class="<?= $classeLida ?>" data-id="<?= $n['id'] ?>">
              <strong><?= htmlspecialchars($n['situacao']) ?></strong><br/>
              <small><?= $data ?> - Status: <?= htmlspecialchars($n['status']) ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="h1-novo">
      <div class="h1-busca">
        <!-- Voltar -->
        <a href="/ocorrenciamain/api/historico_professor.php">
          <button class="btn btn-secondary btn-xs">Voltar</button>
        </a>

        <!-- Filtros -->
        <button type="button" id="concluido" class="btn btn-success btn-xs">Concluído</button>
        <button type="button" id="pendente" class="btn btn-warning btn-xs">Pendente</button>
        <button type="button" id="todos" class="btn btn-xs">Todos</button>

        <!-- Gerar Relatório -->
        <button id="btn-relatorio">Gerar Relatório</button>

        <!-- Busca -->
        <div id="divBusca">
          <input type="text" id="txtBusca" placeholder="Buscar por nome do estudante..." />
          <img src="/ocorrenciamain/img/lupa.png" width="20px" />
        </div>
      </div>
    </div>

    <div class="main-container">
      <div class="table">
        <table>
          <thead>
            <tr>
              <th>Nome</th>
              <th>Problema</th>
              <th>Data</th>
              <th>Professor</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="table-body">
            <?php foreach ($notificacoes as $row): ?>
            <tr data-status="<?= $row['status'] ?>">
              <td><?= htmlspecialchars($row['estudante']) ?></td>
              <td><?= htmlspecialchars($row['situacao']) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($row['data'])) ?></td>
              <td><?= htmlspecialchars($row['professor']) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<script>
document.getElementById("concluido").addEventListener("click", () => filterTable("concluido"));
document.getElementById("pendente").addEventListener("click", () => filterTable("pendente"));
document.getElementById("todos").addEventListener("click", () => filterTable("todos"));

function filterTable(status) {
  document.querySelectorAll("#table-body tr").forEach(row => {
    row.style.display = (status === "todos" || row.dataset.status === status) ? "" : "none";
  });
}

document.getElementById("txtBusca").addEventListener("input", function () {
  let term = this.value.toLowerCase();
  document.querySelectorAll("#table-body tr").forEach(row => {
    let name = row.querySelector("td").textContent.toLowerCase();
    row.style.display = name.includes(term) ? "" : "none";
  });
});

const sino = document.getElementById('notificacao');
const lista = document.getElementById('lista-notificacoes');
const badge = document.getElementById('badge-count');

sino.addEventListener('click', () => {
  lista.style.display = (lista.style.display === 'block') ? 'none' : 'block';
});

document.addEventListener('click', e => {
  if (!sino.contains(e.target) && !lista.contains(e.target)) lista.style.display = 'none';
});

lista.querySelectorAll('li[data-id]').forEach(item => {
  item.addEventListener('click', () => {
    if (item.classList.contains('lida')) return;

    fetch('/ocorrenciamain/api/marcar_visto.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: item.dataset.id })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        item.classList.add('lida');
        let count = parseInt(badge.textContent);
        if (--count <= 0) badge.style.display = 'none';
        else badge.textContent = count;
      } else {
        alert('Erro ao marcar como lida.');
      }
    })
    .catch(() => alert('Erro de requisição.'));
  });
});

document.getElementById('btn-relatorio').addEventListener('click', () => {
  window.open('/ocorrenciamain/api/gerar_relatorio_mensal.php', '_blank');
});
</script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
