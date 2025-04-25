<?php
include 'conexao.php';

// Verifica se a coluna 'status' existe na tabela Ocorrencia
$verifica_coluna = "SHOW COLUMNS FROM Ocorrencia LIKE 'status'";
$result_coluna = $conn->query($verifica_coluna);

if ($result_coluna->num_rows == 0) {
    // Se a coluna não existir, cria ela
    $alter_sql = "ALTER TABLE Ocorrencia ADD COLUMN status ENUM('pendente', 'andamento', 'concluido') NOT NULL DEFAULT 'pendente'";
    $conn->query($alter_sql);
}

// Consulta os dados
$sql = "SELECT Ocorrencia.id, Ocorrencia.estudante, Ocorrencia.situacao, Ocorrencia.data, 
               Professor.nome AS professor, Ocorrencia.status 
        FROM Ocorrencia 
        JOIN Professor ON Ocorrencia.professor_id = Professor.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Ocorrências</title>
    <link rel="stylesheet" href="/ocorrenciamain/public/historico.css">
</head>
<body>

<header>
    <div class="logo-container">
        <img src="/ocorrenciamain/img/brasao-do-ceara.png" alt="Brasão do Ceará" width="70px">
        <p class="texto" style="color: white">
            GOVERNO DO ESTADO DO CEARÁ <br>
            19ª COORDENADORIA REGIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO <br>
            ESCOLA ESTADUAL DE EDUCAÇÃO PROFISSIONAL PAULO BARBOSA LEITE
        </p>
        <img src="/ocorrenciamain/img/escola-removebg-preview.png" alt="Logo da Escola" width="100px">
    </div>
</header>

<main>
    <div class="tudo">
   <!-- Botão Voltar -->
<a href="/ocorrenciamain/public/geral.html" id="voltar">
  <button>Voltar</button>
</a>

<!-- Botões Concluído, Pendente, Todos -->
<div class="h1-busca">
    
  <button type="button" id="concluido">Concluído</button>
  <button type="button" id="pendente">Pendente</button>
  <button type="button" id="todos">Todos</button>
</div>

<!-- Campo de Nova Ocorrência e Busca -->
<div class="h1-novo">
  <!-- Nova Ocorrência -->
  <a href="/ocorrenciamain/public/TelaOcorrencia2.html">
    <button>Nova Ocorrência</button>
  </a>
  
  <!-- Caixa de busca -->
  <div id="divBusca">
    <input type="text" id="txtBusca" placeholder="Buscar...">
    <img src="/ocorrenciamain/img/lupa.png" id="btnBusca" alt="Buscar" width="20px">
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
                            <td><?php echo htmlspecialchars($row['estudante']); ?></td>
                            <td><?php echo htmlspecialchars($row['situacao']); ?></td>
                            <td><?php echo htmlspecialchars($row['data']); ?></td>
                            <td><?php echo htmlspecialchars($row['professor']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    // Filtra os itens com base no status
    document.getElementById("concluido").addEventListener("click", function() {
        filterTable("concluido");
    });

    document.getElementById("pendente").addEventListener("click", function() {
        filterTable("pendente");
    });

    document.getElementById("todos").addEventListener("click", function() {
        filterTable("todos");
    });

    // Função que aplica o filtro
    function filterTable(status) {
        let rows = document.querySelectorAll("#table-body tr");

        rows.forEach(function(row) {
            if (status === "todos") {
                row.style.display = "";
            } else {
                if (row.getAttribute("data-status") === status) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    }

    // Filtro de busca no nome
    document.getElementById("txtBusca").addEventListener("input", function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll("#table-body tr");

        rows.forEach(function(row) {
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
$conn->close();
?>
