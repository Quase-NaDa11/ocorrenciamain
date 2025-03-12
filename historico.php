<?php
include 'conexao.php';

$sql = "SELECT Ocorrencia.id, Ocorrencia.estudante, Ocorrencia.situacao, Ocorrencia.data, Professor.nome AS professor, Ocorrencia.status 
        FROM Ocorrencia 
        JOIN Professor ON Ocorrencia.professor_id = Professor.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Histórico de Ocorrências</title>
    <link rel="stylesheet" href="historico.css" />
  </head>
  <body>
    <header>
      <div class="logo-container">
        <img src="img/brasao-do-ceara.png" alt="" width="70px" />
        <p class="texto" style="color: white">
          GOVERNO DO ESTADO DO CEARÁ <br />
          19ª COORDENADORIA REGIONAL DE DESENVOLVIMENTO DA EDUCAÇÃO <br />
          ESCOLA ESTADUAL DE EDUCAÇÃO PROFISSIONAL PAULO BARBOSA LEITE
        </p>
        <img src="img/escola-removebg-preview.png" alt="" width="100px" />
      </div>
    </header>

    <main>
      <div class="tudo">
        <div class="h1-novo">
          <div class="h1-busca">
            <div class="btn-group">
              <button type="button" id="Concluido" class="btn btn-success btn-xs">Concluído</button>
              <button type="button" id="Andamento" class="btn btn-warning btn-xs">Andamento</button>
              <button type="button" class="btn btn-xs">Todos</button>
            </div>

            <div id="divBusca">
              <input type="text" id="txtBusca" placeholder="Buscar..." />
              <img src="img/lupa.png" id="btnBusca" alt="Buscar" width="20px" />
            </div>
          </div>
          <div class="novo">
            <a href="TelaOcorrencia.html">
              <button>Nova Ocorrência</button>
            </a>
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
            <th>Situação</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['estudante']; ?></td>
            <td><?php echo $row['situacao']; ?></td>
            <td><?php echo $row['data']; ?></td>
            <td><?php echo $row['professor']; ?></td>
            <td data-estado="<?php echo $row['status']; ?>"><?php echo $row['status']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

          </div>
        </div>
      </div>
    </main>

    <script src="historicoscript.js"></script>
  </body>
</html>
