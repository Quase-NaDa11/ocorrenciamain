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
                        Ocorrencia.status
                    FROM Ocorrencia
                    LEFT JOIN Professor ON Ocorrencia.professor_id = Professor.id
                    WHERE Ocorrencia.turma = ?
                    ORDER BY Ocorrencia.data DESC";

$stmt = $conn->prepare($sql_ocorrencias);
$stmt->bind_param("s", $turmaDT);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ocorrências da Minha Turma - Diretor de Turma</title>
<link rel="stylesheet" href="/ocorrenciamain/public/historico.css" />
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
  <div class="tudo">
    <div class="h1-novo">
      <div class="h1-busca">
        <!-- Botões de filtro -->
        <button type="button" id="concluido" class="btn btn-success btn-xs">Concluído</button>
        <button type="button" id="pendente" class="btn btn-warning btn-xs">Pendente</button>
        <button type="button" id="todos" class="btn btn-xs">Todos</button>

        <!-- Voltar para histórico -->
        <div class="btn-group" style="margin-left: 10px;">
          <a href="/ocorrenciamain/api/historico_professor.php" class="btn btn-secondary btn-xs">
            <button>Voltar</button>
          </a>
        </div>

        <!-- Campo de busca -->
        <div id="divBusca">
          <input type="text" id="txtBusca" placeholder="Buscar por nome do estudante..." />
          <img src="/ocorrenciamain/img/lupa.png" id="btnBusca" alt="Buscar" width="20px" />
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
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr data-status="<?php echo htmlspecialchars($row['status']); ?>">
              <td data-label="Nome"><?php echo htmlspecialchars($row['estudante']); ?></td>
              <td data-label="Problema"><?php echo htmlspecialchars($row['situacao']); ?></td>
              <td data-label="Data"><?php echo htmlspecialchars($row['data']); ?></td>
              <td data-label="Professor"><?php echo htmlspecialchars($row['professor']); ?></td>
              <td data-label="Status"><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
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

// Busca no nome do estudante
document.getElementById("txtBusca").addEventListener("input", function () {
  let searchTerm = this.value.toLowerCase();
  let rows = document.querySelectorAll("#table-body tr");
  rows.forEach(row => {
    let name = row.querySelector("td").textContent.toLowerCase();
    if (name.includes(searchTerm)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
});
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
